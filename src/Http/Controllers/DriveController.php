<?php

namespace ME\EmCore\Http\Controllers;

use ME\EmCore\Models\Folder;
use ME\EmCore\Models\Document;
use ME\EmCore\Models\DriveShareVisit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;
use ME\Services\TelegramBotService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Http\Middleware\AuthorizationMiddleware;

class DriveController extends Controller
{
    protected $telegram;

    public function __construct(TelegramBotService $telegram)
    {
        $this->telegram = $telegram;

        $this->middleware('authorization:drive.view')->only(['index']);
        $this->middleware('authorization:drive.folder_create')->only(['createFolder','updateFolder']);
        $this->middleware('authorization:drive.upload')->only(['upload']);
        $this->middleware('authorization:drive.update_file_name')->only(['updateFileName']);
        $this->middleware('authorization:drive.delete')->only(['delete']);
        $this->middleware('authorization:drive.folder_delete')->only(['deleteFolder']);
        $this->middleware('authorization:drive.share')->only(['share', 'shareFolder', 'shareHistory']);
    }

    public function index(Request $request)
    {
        $folders = Folder::where('user_id', Auth::id())
            ->whereNull('parent_id')
            ->with([
                'documents',
                'children.documents',
                'children.children.documents',
            ])
            ->get();
        $currentFolder = null;
        $documents = [];
        if ($request->folder) {
            $currentFolder = Folder::find($request->folder);
            $documents = $currentFolder ? $currentFolder->documents : [];
        }
        return view('em_core::documents.drive', compact('folders', 'currentFolder', 'documents'));
    }

    public function shareHistory(Request $request)
    {
        $shareType = $request->get('type');

        $visitsQuery = DriveShareVisit::with(['document', 'folder'])
            ->orderByRaw('COALESCE(last_visited_at, created_at) DESC');

        if (in_array($shareType, ['file', 'folder'], true)) {
            $visitsQuery->where('share_type', $shareType);
        }

        $visits = $visitsQuery->paginate(20)->withQueryString();

        return view('em_core::documents.share_history', compact('visits', 'shareType'));
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id'
        ]);
        // Prevent folder creation deeper than sub-subfolder (depth > 2)
        if ($request->parent_id) {
            $parent = Folder::find($request->parent_id);
            $depth = 1;
            $ptr = $parent;
            while ($ptr && $ptr->parent_id) {
                $depth++;
                $ptr = $ptr->parent;
            }
            if ($depth >= 2) {
                return back()->withErrors(['Creating sub-subfolders is not allowed.']);
            }
        }
        Folder::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'user_id' => Auth::id(),
        ]);
        return redirect()->back();
    }

    public function updateFolder(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $folder = Folder::findOrFail($id);
        if ($folder->user_id !== Auth::id()) {
            return back()->withErrors(['You do not have permission to update this folder.']);
        }

        $folder->name = trim($request->name);
        $folder->save();

        return back()->with('success', 'Folder renamed successfully.');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'folder_id' => 'required|exists:folders,id',
            'file' => 'required',
            'file.*' => 'file|max:10240', // 10MB per file
        ]);
        $folder = Folder::findOrFail($request->folder_id);
        $files = $request->file('file');
        if (!is_array($files)) $files = [$files];
        if (count($files) > 10) {
            return back()->withErrors(['You can upload a maximum of 10 files at once.']);
        }
        $totalSize = 0;
        foreach ($files as $file) {
            $totalSize += $file->getSize();
        }
        if ($totalSize > 100 * 1024 * 1024) { // 100MB
            return back()->withErrors(['Total upload size cannot exceed 100MB.']);
        }
        foreach ($files as $file) {
            $extension = strtolower((string) $file->getClientOriginalExtension());
            $uuid = (string) Str::uuid();
            $storedName = $extension !== '' ? "{$uuid}.{$extension}" : $uuid;
            $path = "documents/{$folder->id}/{$storedName}";
            Storage::put($path, file_get_contents($file));
            Document::create([
                'folder_id' => $folder->id,
                'name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'user_id' => Auth::id(),
            ]);
        }
        return redirect()->back()->with('folder', $folder->id);
    }

    public function updateFileName(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $document = Document::findOrFail($id);
        if ($document->user_id !== Auth::id()) {
            return back()->withErrors(['You do not have permission to update this file.']);
        }

        $newName = trim($request->name);
        $currentExt = pathinfo($document->name, PATHINFO_EXTENSION);
        $newExt = pathinfo($newName, PATHINFO_EXTENSION);
        if ($currentExt && !$newExt) {
            $newName .= '.' . $currentExt;
        }

        $document->name = $newName;
        $document->save();

        return back()->with('success', 'File name updated successfully.');
    }

    public function preview($storedName)
    {
        $document = Document::where('stored_name', $storedName)
            ->orWhere('name', $storedName)
            ->firstOrFail();
        if (!Storage::exists($document->file_path)) {
            abort(404, 'File not found.');
        }
        $mime = $document->mime_type;
        $content = Storage::get($document->file_path);

        return response($content, 200)->header('Content-Type', $mime);
    }

    public function download($storedName)
    {
        $document = Document::where('stored_name', $storedName)
            ->orWhere('name', $storedName)
            ->firstOrFail();
        if (!Storage::exists($document->file_path)) {
            return back()->withErrors(['File does not exist.']);
        }
        return Storage::download($document->file_path, $document->name);
    }

    public function delete($id)
    {
        $doc = Document::findOrFail($id);
        Storage::delete($doc->file_path); // fix: use file_path
        $doc->delete();
        return back()->with('success', 'File deleted successfully.');
    }

    public function deleteFolder($id)
    {
        $folder = Folder::findOrFail($id);

        // Allow deleting legacy folders where user_id is missing (null),
        // but still block folders explicitly owned by another user.
        if (!is_null($folder->user_id) && (int) $folder->user_id !== (int) Auth::id()) {
            return back()->withErrors(['You do not have permission to delete this folder.']);
        }

        // Recursively delete all documents in this folder and its subfolders
        $this->deleteFolderContents($folder);

        // Delete the folder itself
        $folder->delete();

        return back()->with('success', 'Folder deleted successfully.');
    }

    private function deleteFolderContents($folder)
    {
        // Delete all documents in this folder
        foreach ($folder->documents as $doc) {
            Storage::delete($doc->file_path);
            $doc->delete();
        }

        // Recursively delete contents of subfolders
        foreach ($folder->children as $child) {
            $this->deleteFolderContents($child);
            $child->delete();
        }
    }

    public function share($id, Request $request)
    {
        $request->validate([
            'share_mode' => 'required|in:temporary,permanent',
        ]);

        $doc = Document::findOrFail($id);
        $shareToken = Str::random(32);
        $shareMode = $request->input('share_mode');

        $doc->share_token = $shareToken;
        $doc->share_mode = $shareMode;
        $doc->share_token_created_at = now();
        $doc->share_token_used_at = null;
        $doc->save();

        $link = route('drive.shared.form', ['id' => $doc->id, 'token' => $shareToken]);
        session()->flash('share_link_' . $doc->id, $link);
        session()->flash('share_mode_' . $doc->id, $shareMode);

        $label = $shareMode === 'temporary' ? 'Temporary' : 'Permanent';
        $msg = "Document Share ({$label}) Link: {$link}";
        $this->telegram->sendMessage($msg);

        return back();
    }

    public function sharedAccessForm($id, Request $request)
    {
        $token = $request->get('token');
        $document = Document::findOrFail($id);

        if (!$this->isValidDocumentShare($document, $token)) {
            if (!auth()->check()) {
                abort(404, 'Invalid or expired share link.');
            }
            return redirect()->route('admin.drive')->withErrors(['Invalid or expired share link.']);
        }

        // Temporary link should be usable only once.
        if ($document->share_mode === 'temporary') {
            $document->share_token_used_at = now();
            $document->save();
        }

        $this->recordShareVisit('file', $token, $request, $document, null);

        return view('em_core::documents.otp_result', [
            'document' => $document,
            'shareMode' => $document->share_mode,
            'shareToken' => $token,
            'enableAutoRefresh' => $document->share_mode === 'temporary',
        ]);
    }

    public function sharedHeartbeat($id, Request $request)
    {
        $token = $request->get('token');
        $document = Document::find($id);

        $isValid = $document && $this->isValidDocumentShare($document, $token);

        return response()->json([
            'ok' => true,
            'valid' => (bool) $isValid,
            'checked_at' => now()->toDateTimeString(),
        ]);
    }

    public function verifyOtp($id, Request $request)
    {
        // File sharing no longer uses OTP. Keep this endpoint for backward compatibility.
        return redirect()->route('drive.shared.form', [
            'id' => $id,
            'token' => $request->input('token'),
        ]);
    }

    public function shareFolder($id, Request $request)
    {
        $folder = Folder::findOrFail($id);
        $otp = rand(100000, 999999);
        $shareToken = Str::random(32);

        Cache::put("share_folder_{$shareToken}_otp", $otp, now()->addMinutes(30));
        Cache::put("share_folder_{$shareToken}_folder", $folder->id, now()->addMinutes(30));

        $link = route('drive.folder.shared.form', ['id' => $folder->id, 'token' => $shareToken]);
        session()->flash('share_folder_link_' . $folder->id, $link);
        session()->flash('share_folder_otp_' . $folder->id, $otp);

        // Send OTP and link to Telegram
        $msg = "Folder Share OTP: {$otp}\nLink: {$link}";
        $this->telegram->sendMessage($msg);

        return back();
    }

    public function sharedFolderAccessForm($id, Request $request)
    {
        $token = $request->get('token');
        if (!Cache::has("share_folder_{$token}_otp") || !Cache::has("share_folder_{$token}_folder")) {
            if (!auth()->check()) {
                abort(404, 'Invalid OTP or expired link.');
            }
            return redirect()->route('admin.drive')->withErrors(['Invalid or expired share link.']);
        }

        $folder = Folder::findOrFail($id);
        $this->recordShareVisit('folder', $token, $request, null, $folder);

        return view('em_core::documents.folder_otp', ['folder_id' => $id, 'token' => $token]);
    }

    public function verifyFolderOtp($id, Request $request)
    {
        $otp = $request->input('otp');
        $token = $request->input('token');
        $expectedOtp = Cache::get("share_folder_{$token}_otp");
        $folderId = Cache::get("share_folder_{$token}_folder");

        if ($expectedOtp && $folderId == $id && $otp == $expectedOtp) {
            $folder = Folder::findOrFail($id);
            $documents = $folder->documents;
            Cache::forget("share_folder_{$token}_otp");
            Cache::forget("share_folder_{$token}_folder");
            return view('em_core::documents.folder_otp_result', ['folder' => $folder, 'documents' => $documents]);
        } else {
            if (!auth()->check()) {
                abort(404, 'Invalid OTP or expired link.');
            }
            return back()->withErrors(['Invalid OTP or expired link.']);
        }
    }

    private function isValidDocumentShare(Document $document, ?string $token): bool
    {
        if (!$token || !$document->share_token || $document->share_token !== $token) {
            return false;
        }

        if (!in_array($document->share_mode, ['temporary', 'permanent'], true)) {
            return false;
        }

        if ($document->share_mode === 'temporary' && $document->share_token_used_at) {
            return false;
        }

        return true;
    }

    private function recordShareVisit(string $shareType, ?string $token, Request $request, ?Document $document = null, ?Folder $folder = null): void
    {
        $ip = $this->resolveClientPublicIp($request);
        $userAgent = (string) $request->userAgent();
        $browser = $this->detectBrowser($userAgent);
        $os = $this->detectOs($userAgent);
        $deviceType = $this->detectDeviceType($userAgent);
        $deviceName = $this->detectDeviceName($userAgent);

        $visit = DriveShareVisit::firstOrNew([
            'share_type' => $shareType,
            'share_token' => $token,
            'ip_address' => $ip,
        ]);

        $visit->document_id = $document ? $document->id : null;
        $visit->folder_id = $folder ? $folder->id : null;
        $visit->visited_url = (string) $request->fullUrl();
        $visit->referer = (string) $request->headers->get('referer');
        $visit->user_agent = $userAgent;
        $visit->browser = $browser;
        $visit->os = $os;
        $visit->device_type = $deviceType;
        $visit->device_name = $deviceName;

        if (!$visit->exists) {
            $visit->first_visited_at = now();
            $visit->visit_count = 1;
        } else {
            $visit->visit_count = ((int) $visit->visit_count) + 1;
        }

        $visit->last_visited_at = now();
        $visit->save();
    }

    private function resolveClientPublicIp(Request $request): string
    {
        $candidates = [];

        $forwardedFor = (string) $request->headers->get('x-forwarded-for', '');
        if ($forwardedFor !== '') {
            $parts = array_map('trim', explode(',', $forwardedFor));
            $candidates = array_merge($candidates, $parts);
        }

        $headerKeys = [
            'cf-connecting-ip',
            'x-real-ip',
            'true-client-ip',
            'client-ip',
        ];

        foreach ($headerKeys as $key) {
            $value = trim((string) $request->headers->get($key, ''));
            if ($value !== '') {
                $candidates[] = $value;
            }
        }

        $requestIp = trim((string) $request->ip());
        if ($requestIp !== '') {
            $candidates[] = $requestIp;
        }

        foreach ($candidates as $candidate) {
            if ($this->isValidPublicIp($candidate)) {
                return $candidate;
            }
        }

        foreach ($candidates as $candidate) {
            if (filter_var($candidate, FILTER_VALIDATE_IP)) {
                return $candidate;
            }
        }

        return 'unknown';
    }

    private function isValidPublicIp(string $ip): bool
    {
        return (bool) filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    private function detectBrowser(string $ua): string
    {
        $map = [
            'Edg/' => 'Microsoft Edge',
            'OPR/' => 'Opera',
            'Chrome/' => 'Chrome',
            'Firefox/' => 'Firefox',
            'Safari/' => 'Safari',
            'MSIE ' => 'Internet Explorer',
            'Trident/' => 'Internet Explorer',
        ];

        foreach ($map as $needle => $label) {
            if (stripos($ua, $needle) !== false) {
                return $label;
            }
        }

        return 'Unknown';
    }

    private function detectOs(string $ua): string
    {
        $map = [
            'Windows NT' => 'Windows',
            'Mac OS X' => 'macOS',
            'Android' => 'Android',
            'iPhone' => 'iOS',
            'iPad' => 'iPadOS',
            'Linux' => 'Linux',
        ];

        foreach ($map as $needle => $label) {
            if (stripos($ua, $needle) !== false) {
                return $label;
            }
        }

        return 'Unknown';
    }

    private function detectDeviceType(string $ua): string
    {
        if (stripos($ua, 'iPad') !== false || stripos($ua, 'Tablet') !== false) {
            return 'Tablet';
        }
        if (stripos($ua, 'Mobile') !== false || stripos($ua, 'Android') !== false || stripos($ua, 'iPhone') !== false) {
            return 'Mobile';
        }
        return 'Desktop';
    }

    private function detectDeviceName(string $ua): string
    {
        if (preg_match('/\\(([^\\)]+)\\)/', $ua, $matches)) {
            return Str::limit($matches[1], 120, '');
        }

        return 'Unknown Device';
    }
}
