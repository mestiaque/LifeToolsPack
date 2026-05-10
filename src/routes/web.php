<?php

use Illuminate\Support\Facades\Route;
use ME\EmCore\Http\Controllers\BirthdayController;
use ME\EmCore\Http\Controllers\DailyExpenseController;
use ME\EmCore\Http\Controllers\DashboardController;
use ME\EmCore\Http\Controllers\DiskController;
use ME\EmCore\Http\Controllers\LinkController;
use ME\EmCore\Http\Controllers\DriveController;
use ME\EmCore\Http\Controllers\EventController;
use ME\EmCore\Http\Controllers\EventExpenseController;
use ME\EmCore\Http\Controllers\FrontController;
use ME\EmCore\Http\Controllers\GalleryController;
use ME\EmCore\Http\Controllers\HerCycleController;
use ME\EmCore\Http\Controllers\LoanController;
use ME\EmCore\Http\Controllers\LoanUserController;
use ME\EmCore\Http\Controllers\MessageController;
use ME\EmCore\Http\Controllers\NotifyPersonController;
use ME\EmCore\Http\Controllers\NoteController;
use ME\EmCore\Http\Controllers\RoleController;
use ME\EmCore\Http\Controllers\SettingController;
use ME\EmCore\Http\Controllers\UserController;
use ME\Http\Middleware\LocaleMiddleware;



Route::middleware(['web'])->group(function () {

    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    //gallery
    Route::middleware([LocaleMiddleware::class])->group(function () {
        Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
        Route::post('/gallery/upload', [GalleryController::class, 'upload'])->name('gallery.upload');
        Route::get('/gallery/preview/{id}', [GalleryController::class, 'preview'])->name('gallery.preview');
        Route::get('/drive/shared/{id}', [DriveController::class, 'sharedAccessForm'])->name('drive.shared.form');
        Route::get('/drive/shared/{id}/heartbeat', [DriveController::class, 'sharedHeartbeat'])->name('drive.shared.heartbeat');
        Route::post('/drive/shared/{id}/verify', [DriveController::class, 'verifyOtp'])->name('drive.shared.verify');
        Route::get('/drive/folder/shared/{id}', [DriveController::class, 'sharedFolderAccessForm'])->name('drive.folder.shared.form');
        Route::post('/drive/folder/shared/{id}/verify', [DriveController::class, 'verifyFolderOtp'])->name('drive.folder.shared.verify');
        Route::get('/drive/preview/{stored_name}', [DriveController::class, 'preview'])->name('drive.preview');
        Route::get('/drive/download/{stored_name}', [DriveController::class, 'download'])->name('drive.download');

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

        Route::get('/notify-people/trigger', [NotifyPersonController::class, 'trigger'])->name('notify-people.trigger');

    });


    Route::middleware(['auth', LocaleMiddleware::class])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/admin', [DashboardController::class, 'index']);
        Route::group([ 'prefix' => 'admin', 'as' => 'admin.'], function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
            Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

            Route::resource('users', UserController::class);
            Route::resource('roles', RoleController::class);
            Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

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
            Route::get('/drive/share-history', [DriveController::class, 'shareHistory'])->name('drive.share.history');
            Route::post('/drive/folder', [DriveController::class, 'createFolder'])->name('drive.folder.create');
            Route::put('/drive/folder/update/{id}', [DriveController::class, 'updateFolder'])->name('drive.folder.update');
            Route::delete('/drive/folder/delete/{id}', [DriveController::class, 'deleteFolder'])->name('drive.folder.delete');
            Route::post('/drive/upload', [DriveController::class, 'upload'])->name('drive.upload');
            Route::put('/drive/file/update/{id}', [DriveController::class, 'updateFileName'])->name('drive.file.update');
            Route::delete('/drive/delete/{id}', [DriveController::class, 'delete'])->name('drive.delete');
            // Share & OTP routes for documents
            Route::post('/drive/share/{id}', [DriveController::class, 'share'])->name('drive.share');
            Route::get('/drive/preview/{stored_name}', [DriveController::class, 'preview'])->name('drive.preview');
            Route::get('/drive/download/{stored_name}', [DriveController::class, 'download'])->name('drive.download');
            // Share & OTP routes for folders and subfolders
            Route::post('/drive/folder/share/{id}', [DriveController::class, 'shareFolder'])->name('drive.folder.share');

            // Loan routes
            Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
            Route::get('/loans/payment-planner', [LoanController::class, 'paymentPlanner'])->name('loans.payment-planner');
            Route::get('/loans/history/{user}', [LoanController::class, 'history'])->name('loans.history');
            Route::get('/loans/user-history/{user}', [LoanController::class, 'userHistory'])->name('loans.user-history');

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

            // Event Expenses routes
            Route::get('events/{event}/expenses', [EventExpenseController::class, 'index'])->name('events.expenses.index');
            Route::get('events/{event}/expenses/print', [EventExpenseController::class, 'print'])->name('events.expenses.print');
            Route::post('events/{event}/expenses', [EventExpenseController::class, 'store'])->name('events.expenses.store');
            Route::put('events/{event}/expenses/{id}', [EventExpenseController::class, 'update'])->name('events.expenses.update');
            Route::delete('events/{event}/expenses/{id}', [EventExpenseController::class, 'destroy'])->name('events.expenses.delete');

            // HerCycle routes
            Route::get('/hercycle', [HerCycleController::class, 'index'])->name('hercycle.index');
            Route::get('/hercycle/setup', [HerCycleController::class, 'setup'])->name('hercycle.setup');
            Route::post('/hercycle/profile', [HerCycleController::class, 'storeProfile'])->name('hercycle.profile.store');
            Route::put('/hercycle/profile/{id}', [HerCycleController::class, 'updateProfile'])->name('hercycle.profile.update');
            Route::post('/hercycle/period', [HerCycleController::class, 'storePeriod'])->name('hercycle.period.store');
            Route::put('/hercycle/period/{id}', [HerCycleController::class, 'updatePeriod'])->name('hercycle.period.update');
            Route::delete('/hercycle/period/{id}', [HerCycleController::class, 'deletePeriod'])->name('hercycle.period.delete');
            Route::post('/hercycle/symptom', [HerCycleController::class, 'storeSymptom'])->name('hercycle.symptom.store');
            Route::put('/hercycle/notifications/{id}', [HerCycleController::class, 'updateNotifications'])->name('hercycle.notifications.update');
            Route::get('/hercycle/month-data', [HerCycleController::class, 'getMonthData'])->name('hercycle.month-data');
            Route::post('/hercycle/send-notifications', [HerCycleController::class, 'sendNotifications'])->name('hercycle.sendNotifications');

            Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
            Route::get('/get-notes', [NoteController::class, 'getNotes']);
            Route::post('/notes', [NoteController::class, 'store']);
            Route::put('/notes/{note}', [NoteController::class, 'update']);
            Route::delete('/notes/{note}', [NoteController::class, 'destroy']);

            Route::resource('links', LinkController::class)->except(['show', 'create', 'edit']);
            Route::resource('notify-people', NotifyPersonController::class)->except(['show', 'create', 'edit']);

        });

        Route::get('/calendar/events', [DashboardController::class, 'calendarEvents']);
        Route::post('/calendar/events/store', [DashboardController::class, 'calendarStore']);
        Route::post('/calendar/events/update', [DashboardController::class, 'calendarUpdate']);
        Route::post('/calendar/events/delete', [DashboardController::class, 'calendarDelete']);
    });

    include 'file.php';
});


Route::get('sitemap.xml', [FrontController::class, 'sitemap'])->name('sitemap');
