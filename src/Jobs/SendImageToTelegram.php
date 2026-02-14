<?php

namespace EmCore\Jobs;

use EmCore\Services\TelegramBotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SendImageToTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileName;
    protected $originalName;

    public function __construct($fileName, $originalName)
    {
        $this->fileName = $fileName;
        $this->originalName = $originalName;
    }

    public function handle()
    {
        $telegram = new TelegramBotService();
        $caption  = 'New image uploaded: ' . $this->originalName;
        $localPath = Storage::path('gallery/' . $this->fileName);

        try {
            $telegram->sendPhotoFile($localPath, $caption);
        } catch (\Exception $ex) {
            // optionally log the error
        }
    }
}
