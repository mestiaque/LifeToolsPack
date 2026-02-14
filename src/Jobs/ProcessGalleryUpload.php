<?php
namespace ME\EmCore\Jobs;

use Log;
use ME\EmCore\Models\Gallery;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessGalleryUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $files;

    public function __construct(array $files)
    {
        $this->files = $files;
    }

    public function handle_old()
    {
            Log::info('ProcessGalleryUpload started', ['files' => $this->files]);

        foreach ($this->files as $file) {

            $fileName = 'img-' . Str::uuid() . '.' . $file['extension'];

            // MOVE → final folder (no change to resolution)
            Storage::move($file['temp_path'], 'gallery/' . $fileName);

            // Save DB entry
            Gallery::create([
                'img' => $fileName,
                'original_name' => $file['original_name'],
                'is_public' => $file['is_public']
            ]);

                    \Log::info('File processed', ['file' => $fileName]);


            // Send Telegram only if private
            if (!$file['is_public']) {
                SendImageToTelegram::dispatch($fileName, $file['original_name']);
            }
        }
            \Log::info('ProcessGalleryUpload completed');

    }

        public function handle()
        {
            foreach ($this->files as $file) {
                $fileName = 'img-' . Str::uuid() . '.' . $file['extension'];

                // Ensure private gallery folder exists
                if (!Storage::exists('gallery')) {
                    Storage::makeDirectory('gallery');
                }

                // Move temp → private gallery
                Storage::move($file['temp_path'], 'gallery/' . $fileName);

                // DB insert
                $imageModel = Gallery::create([
                    'img' => $fileName,
                    'original_name' => $file['original_name'],
                    'is_public' => $file['is_public']
                ]);

                // Telegram if private
                if (!$file['is_public']) {
                    SendImageToTelegram::dispatch($fileName, $file['original_name']);
                }
            }
        }
}
