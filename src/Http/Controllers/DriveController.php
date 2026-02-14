<?php

namespace EmCore\Http\Controllers;

use EmCore\Models\Folder;
use EmCore\Models\Document;
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
        $this->middleware('authorization:drive.folder.create')->only(['createFolder']);
        $this->middleware('authorization:drive.upload')->only(['upload']);
        $this->middleware('authorization:drive.delete')->only(['delete']);
        $this->middleware('authorization:drive.share')->only(['share', 'shareFolder']);
    }

    public function index(Request $request)
    {
        $folders = Folder::where('user_id', Auth::id())->whereNull('parent_id')->with('children')->get();
        $currentFolder = null;
        $documents = [];
        if ($request->folder) {
            $currentFolder = Folder::find($request->folder);
            $documents = $currentFolder ? $currentFolder->documents : [];
        }
        return view('em_core::documents.drive', compact('folders', 'currentFolder', 'documents'));
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
            $filename = Str::random(16) . '_' . $file->getClientOriginalName();
            $path = "documents/{$folder->id}/{$filename}";
            Storage::put($path, file_get_contents($file));
            Document::create([
                'folder_id' => $folder->id,
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'user_id' => Auth::id(),
            ]);
        }
        return redirect()->back()->with('folder', $folder->id);
    }

    public function preview($id)
    {
        $document = Document::findOrFail($id);
        if (!Storage::exists($document->file_path)) {
            abort(404, 'File not found.');
        }
        $mime = $document->mime_type;
        $content = Storage::get($document->file_path);
        return response($content, 200)->header('Content-Type', $mime);
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
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

    public function share($id, Request $request)
    {
        $doc = Document::findOrFail($id);
        $otp = rand(100000, 999999);
        $shareToken = Str::random(32);

        // Store OTP and token in cache for 10 minutes
        Cache::put("share_{$shareToken}_otp", $otp, now()->addMinutes(30));
        Cache::put("share_{$shareToken}_doc", $doc->id, now()->addMinutes(30));

        $link = route('drive.shared.form', ['id' => $doc->id, 'token' => $shareToken]);
        session()->flash('share_link_' . $doc->id, $link);
        session()->flash('share_otp_' . $doc->id, $otp); // Show OTP for demo

        // Send OTP and link to Telegram
        $msg = "Document Share OTP: {$otp}\nLink: {$link}";
        $this->telegram->sendMessage($msg);

        return back();
    }

    public function sharedAccessForm($id, Request $request)
    {
        $token = $request->get('token');
        // Check if token exists
        if (!Cache::has("share_{$token}_otp") || !Cache::has("share_{$token}_doc")) {
            if (!auth()->check()) {
                abort(404, 'Invalid OTP or expired link.');
            }
            return redirect()->route('admin.drive')->withErrors(['Invalid or expired share link.']);
        }
        return view('em_core::documents.otp', ['doc_id' => $id, 'token' => $token]);
    }

    public function verifyOtp($id, Request $request)
    {
        $otp = $request->input('otp');
        $token = $request->input('token');
        $expectedOtp = Cache::get("share_{$token}_otp");
        $docId = Cache::get("share_{$token}_doc");

        if ($expectedOtp && $docId == $id && $otp == $expectedOtp) {
            $document = Document::findOrFail($id);
            // Optionally, clear OTP after use
            Cache::forget("share_{$token}_otp");
            Cache::forget("share_{$token}_doc");
            // Show image preview and download button in blade
            return view('em_core::documents.otp_result', ['document' => $document]);
        } else {
            if (!auth()->check()) {
                abort(404, 'Invalid OTP or expired link.');
            }
            return back()->withErrors(['Invalid OTP or expired link.']);
        }
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
}

