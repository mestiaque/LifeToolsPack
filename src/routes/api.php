<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\DocumentController;
use ME\EmCore\Http\Controllers\TelegramWebhookController;

Route::prefix('api')->group(function () {
});
Route::post('/telegram/webhook/{secret}', [TelegramWebhookController::class, 'handle']);
