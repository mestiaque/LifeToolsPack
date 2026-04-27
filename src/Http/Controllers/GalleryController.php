<?php

namespace ME\EmCore\Http\Controllers;

use Exception;
use ME\EmCore\Models\Gallery;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;
use ME\Services\TelegramBotService;
use Illuminate\Support\Facades\Auth;
use ME\EmCore\Jobs\ProcessGalleryUpload;
use Illuminate\Support\Facades\Storage;
use App\Http\Middleware\AuthorizationMiddleware;

class GalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:gallery.show')->only(['index', 'preview', 'show']);
        $this->middleware('authorization:gallery.create')->only(['upload']);
        $this->middleware('authorization:gallery.privacy')->only(['togglePrivacy']);
        $this->middleware('authorization:gallery.delete')->only(['delete']);

    }

    public function index(Request $request)
    {
        if(Auth::check()) {
            $query = Gallery::orderBy('id', 'desc');
            if ($request->has('is_public') && $request->is_public !== null && $request->is_public !== '') {
                $query->where('is_public', $request->is_public);
            }
            $images = $query->get();
            return view('em_core::gallery.index', compact('images'));

        }else{
            $images = Gallery::where('is_public', 1)->orderBy('id', 'desc')->get();
            return view('em_core::gallery.guest-index', compact('images'));
        }
        return view('em_core::auth.unauthorize');
    }

    public function upload_old(Request $request)
    {
        try {
            if ($request->hasFile('images')) {
                $uploaded = [];
                $isPublic = $request->input('is_public', 0);
                $telegram = new TelegramBotService();
                foreach ($request->file('images') as $image) {
                    $fileName = 'img-' . Str::uuid() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('gallery', $fileName); // saves to storage/app/gallery
                    $uploaded[] = Gallery::create([
                        'img' => $fileName,
                        'original_name' => $image->getClientOriginalName(),
                        'is_public' => $isPublic
                    ]);
                    // Send image to Telegram
                    $photoUrl = url('storage/gallery/' . $fileName); // uses app_url from .env, ensure it's HTTPS
                    $caption = 'New image uploaded: ' . $image->getClientOriginalName();
                    if (!$isPublic) {
                        $response = $telegram->sendPhoto($photoUrl, $caption);
                    }
                    // If Telegram fails (e.g. local URL), send file directly
                    if (!$response->ok()) {
                        $localPath = Storage::path('gallery/' . $fileName);
                        $telegram->sendPhotoFile($localPath, $caption);
                    }
                }
                return response()->json(['success' => true, 'uploaded' => $uploaded]);
            }
            throw new Exception('No images uploaded');
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function upload_old_2(Request $request)
    {
        try {
            if ($request->hasFile('images')) {
                $uploaded = [];
                $isPublic = (int) $request->input('is_public', 0);
                $telegram = new TelegramBotService();

                foreach ($request->file('images') as $image) {
                    $fileName = 'img-' . Str::uuid() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('gallery', $fileName); // saves to storage/app/gallery

                    $uploaded[] = Gallery::create([
                        'img' => $fileName,
                        'original_name' => $image->getClientOriginalName(),
                        'is_public' => $isPublic
                    ]);

                    // ✅ Only send to Telegram if NOT public
                    if (!$isPublic) {
                        $photoUrl = url('storage/gallery/' . $fileName);
                        $caption  = 'New image uploaded: ' . $image->getClientOriginalName();

                        try {
                            $response = $telegram->sendPhoto($photoUrl, $caption);

                            // If Telegram fails (e.g. local URL), send file directly
                            if (!$response || !$response->ok()) {
                                $localPath = Storage::path('gallery/' . $fileName);
                                $telegram->sendPhotoFile($localPath, $caption);
                            }
                        } catch (\Exception $ex) {
                            // fallback if sendPhoto throws error
                            $localPath = Storage::path('gallery/' . $fileName);
                            $telegram->sendPhotoFile($localPath, $caption);
                        }
                    }
                }

                return response()->json(['success' => true, 'uploaded' => $uploaded]);
            }

            throw new \Exception('No images uploaded');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function upload(Request $request)
    {
        try {
            if (!$request->hasFile('images')) {
                throw new \Exception('No images uploaded');
            }

            $isPublic = (int) $request->input('is_public', 0);
            $tempFiles = [];


            foreach ($request->file('images') as $image) {
                $extension = strtolower($image->getClientOriginalExtension());
                $tempPath = $image->store('tmp_uploads');

                $tempFiles[] = [
                    'temp_path' => $tempPath,
                    'original_name' => $image->getClientOriginalName(),
                    'extension' => $extension,
                    'is_public' => $isPublic,
                ];
            }

            // ONE JOB → all backend processing
            ProcessGalleryUpload::dispatch($tempFiles);

            return response()->json([
                'success' => true,
                'message' => 'Upload started',
                'uploaded' => 'Upload started'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }


    public function preview($id)
    {
        $image = Gallery::findOrFail($id);
        if (!Storage::exists('gallery/' . $image->img)) {
            abort(404, 'File not found.');
        }
        $mime = $image->mime_type;
        $content = Storage::get('gallery/' . $image->img);
        return response($content, 200)->header('Content-Type', $mime);
    }

    public function show($id)
    {
        $image = Gallery::findOrFail($id);
        return response()->json($image);
    }

    public function togglePrivacy(Request $request)
    {
        $image = Gallery::find($request->id);
        if(!$image) {
            return response()->json(['success' => false, 'message' => 'Image not found'], 404);
        }
        $image->is_public = $image->is_public ? 0 : 1;
        $image->save();
        return response()->json(['success' => true, 'is_public' => $image->is_public]);
    }

    public function delete(Request $request)
    {
        $image = Gallery::find($request->id);
        if(!$image) {
            return response()->json(['success' => false, 'message' => 'Image not found'], 404);
        }
        // Delete file from storage
        $imgPath = 'gallery/' . $image->img;
        if(\Illuminate\Support\Facades\Storage::exists($imgPath)) {
            \Illuminate\Support\Facades\Storage::delete($imgPath);
        }
        $image->delete();
        return response()->json(['success' => true]);
    }
}
