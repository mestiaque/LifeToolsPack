<?php

use Illuminate\Support\Facades\Route;
use ME\Http\Middleware\LocaleMiddleware;
use ME\EmCore\Http\Controllers\DiskController;
use ME\EmCore\Http\Controllers\LoanController;
use ME\EmCore\Http\Controllers\RoleController;
use ME\EmCore\Http\Controllers\UserController;
use ME\EmCore\Http\Controllers\DriveController;
use ME\EmCore\Http\Controllers\EventController;
use ME\EmCore\Http\Controllers\FrontController;
use ME\EmCore\Http\Controllers\GalleryController;
use ME\EmCore\Http\Controllers\MessageController;
use ME\EmCore\Http\Controllers\SettingController;
use ME\EmCore\Http\Controllers\BirthdayController;
use ME\EmCore\Http\Controllers\LoanUserController;
use ME\EmCore\Http\Controllers\DashboardController;
use ME\EmCore\Http\Controllers\DailyExpenseController;


Route::middleware(['web'])->group(function () {

    Route::get('/', [FrontController::class, 'index'])->name('home');
    Route::get('/cv', [FrontController::class, 'cv']);
    Route::get('/curriculum-vitae', [FrontController::class, 'cv'])->name('cv');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    //gallery
    Route::middleware([LocaleMiddleware::class])->group(function () {
        Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
        Route::post('/gallery/upload', [GalleryController::class, 'upload'])->name('gallery.upload');
        Route::get('/gallery/preview/{id}', [GalleryController::class, 'preview'])->name('gallery.preview');
        Route::get('/drive/shared/{id}', [DriveController::class, 'sharedAccessForm'])->name('drive.shared.form');
        Route::post('/drive/shared/{id}/verify', [DriveController::class, 'verifyOtp'])->name('drive.shared.verify');
        Route::get('/drive/folder/shared/{id}', [DriveController::class, 'sharedFolderAccessForm'])->name('drive.folder.shared.form');
        Route::post('/drive/folder/shared/{id}/verify', [DriveController::class, 'verifyFolderOtp'])->name('drive.folder.shared.verify');
        Route::get('/drive/preview/{id}', [DriveController::class, 'preview'])->name('drive.preview');
        Route::get('/drive/download/{id}', [DriveController::class, 'download'])->name('drive.download');

        Route::get('/img', [GalleryController::class, 'index']);
        Route::get('/mi', [GalleryController::class, 'index']);
        Route::get('/me', [GalleryController::class, 'index']);

        Route::get('/hbd/{name}', [BirthdayController::class, 'wish'])->name('hbd.wish');
        Route::get('/games', fn () => view('em_core::games.index'))->name('games.index');
        Route::get('/games/bounce', fn () => view('em_core::games.bounce'))->name('games.bounce');
        Route::get('/games/bricks-break', fn () => view('em_core::games.bricks-break'))->name('games.bricks-break');
        Route::get('/games/carrom', fn () => view('em_core::games.carrom'))->name('games.carrom');
        Route::get('/games/snake', fn () => view('em_core::games.snake'))->name('games.snake');
        Route::get('/games/egg-catching', fn () => view('em_core::games.egg-catching'))->name('games.egg-catching');
        Route::get('/games/memory-card', fn () => view('em_core::games.memory-card'))->name('games.memory-card');

    });


    Route::middleware(['auth', LocaleMiddleware::class])->group(function () {

        Route::group([ 'prefix' => 'admin', 'as' => 'admin.'], function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
            Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

            Route::resource('users', UserController::class);
            Route::resource('roles', RoleController::class);

            Route::resource('daily-expenses', DailyExpenseController::class);
            Route::resource('disks', DiskController::class);
            Route::resource('messages', MessageController::class)->except(['show']);
            Route::post('messages/{message}/read', [MessageController::class, 'read'])->name('messages.read');
            Route::get('messages/read-all', [MessageController::class, 'readAll'])->name('messages.readAll');

            Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
            Route::post('/gallery/upload', [GalleryController::class, 'upload'])->name('gallery.upload');
            Route::post('/gallery/toggle-privacy', [GalleryController::class, 'togglePrivacy'])->name('gallery.togglePrivacy');
            Route::post('/gallery/delete', [GalleryController::class, 'delete'])->name('gallery.delete');

            // Drive routes
            Route::get('/drive', [DriveController::class, 'index'])->name('drive');
            Route::post('/drive/folder', [DriveController::class, 'createFolder'])->name('drive.folder.create');
            Route::post('/drive/upload', [DriveController::class, 'upload'])->name('drive.upload');
            Route::delete('/drive/delete/{id}', [DriveController::class, 'delete'])->name('drive.delete');
            // Share & OTP routes for documents
            Route::post('/drive/share/{id}', [DriveController::class, 'share'])->name('drive.share');
            Route::get('/drive/preview/{id}', [DriveController::class, 'preview'])->name('drive.preview');
            Route::get('/drive/download/{id}', [DriveController::class, 'download'])->name('drive.download');
            // Share & OTP routes for folders and subfolders
            Route::post('/drive/folder/share/{id}', [DriveController::class, 'shareFolder'])->name('drive.folder.share');

            // Loan routes
            Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
            Route::get('/loans/history/{user}', [LoanController::class, 'history'])->name('loans.history');

            Route::get('/loans/create', [LoanController::class, 'createLoan'])->name('loans.create');
            Route::post('/loans/store', [LoanController::class, 'storeLoan'])->name('loans.store');
            Route::get('/loans/edit/{id}', [LoanController::class, 'editLoan'])->name('loans.edit');
            Route::post('/loans/update/{id}', [LoanController::class, 'updateLoan'])->name('loans.update');
            Route::post('/loans/delete/{id}', [LoanController::class, 'deleteLoan'])->name('loans.delete');

            Route::post('/repayments/store', [LoanController::class, 'storeRepayment'])->name('repayments.store');
            Route::post('/repayments/update/{id}', [LoanController::class, 'updateRepayment'])->name('repayments.update');
            Route::post('/repayments/delete/{id}', [LoanController::class, 'deleteRepayment'])->name('repayments.delete');

            // LoanUser routes
            Route::resource('loan-users', LoanUserController::class)->except(['show', 'create', 'edit']);

            Route::get('events', [EventController::class, 'index'])->name('events.index');
            Route::post('events', [EventController::class, 'store'])->name('events.store');
            Route::put('events/{id}', [EventController::class, 'update'])->name('events.update');
            Route::delete('events/{id}', [EventController::class, 'destroy'])->name('events.delete');
        });

        Route::get('/calendar/events', [DashboardController::class, 'calendarEvents']);
        Route::post('/calendar/events/store', [DashboardController::class, 'calendarStore']);
        Route::post('/calendar/events/update', [DashboardController::class, 'calendarUpdate']);
        Route::post('/calendar/events/delete', [DashboardController::class, 'calendarDelete']);
    });

    include 'file.php';
});


Route::get('sitemap.xml', [FrontController::class, 'sitemap'])->name('sitemap');

