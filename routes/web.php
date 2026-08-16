<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\PhotoController as AdminPhotoController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\ResidentController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PublicEventController;
use App\Http\Controllers\Public\ResidentUploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Album Foto Perumahan)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events/{uuid}', [PublicEventController::class, 'show'])->name('events.show');
Route::get('/login', fn() => redirect()->route('admin.login'))->name('login');

// No-login Resident Upload Routes (Secure Token System)
Route::get('/upload/{token}', [ResidentUploadController::class, 'show'])->name('resident.upload');
Route::post('/upload/{token}', [ResidentUploadController::class, 'store'])->name('resident.upload.store');
Route::get('/upload/{token}/sukses', [ResidentUploadController::class, 'success'])->name('resident.upload.success');

// Web Database Setup / Migration Route
Route::match(['GET', 'POST'], '/setup-db', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        $seedOutput = \Illuminate\Support\Facades\Artisan::output();

        return response("<h2>✅ Database Berhasil Diinisialisasi & Dimigrasi!</h2><pre>{$migrateOutput}\n{$seedOutput}</pre><br><a href='/'>👉 Menuju Beranda</a>", 200)
            ->header('Content-Type', 'text/html');
    } catch (\Exception $e) {
        return response("<h2>❌ Gagal Migrasi Database:</h2><pre>" . $e->getMessage() . "</pre><br><a href='/'>Kembali</a>", 500)
            ->header('Content-Type', 'text/html');
    }
});

/*
|--------------------------------------------------------------------------
| Admin Auth & Management Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Resident Management
        Route::get('/residents', [ResidentController::class, 'index'])->name('admin.residents.index');
        Route::post('/residents', [ResidentController::class, 'store'])->name('admin.residents.store');
        Route::put('/residents/{resident}', [ResidentController::class, 'update'])->name('admin.residents.update');
        Route::post('/residents/{resident}/toggle-status', [ResidentController::class, 'toggleStatus'])->name('admin.residents.toggle_status');
        Route::post('/residents/{resident}/regenerate-token', [ResidentController::class, 'regenerateToken'])->name('admin.residents.regenerate_token');
        Route::delete('/residents/{resident}', [ResidentController::class, 'destroy'])->name('admin.residents.destroy');

        // QR Code Routes
        Route::get('/residents/qr-print', [QrCodeController::class, 'printAll'])->name('admin.residents.qr_print');
        Route::get('/residents/{resident}/qr', [QrCodeController::class, 'show'])->name('admin.residents.qr');

        // Event Management
        Route::get('/events', [AdminEventController::class, 'index'])->name('admin.events.index');
        Route::get('/events/create', [AdminEventController::class, 'create'])->name('admin.events.create');
        Route::post('/events', [AdminEventController::class, 'store'])->name('admin.events.store');
        Route::get('/events/{event}', [AdminEventController::class, 'show'])->name('admin.events.show');
        Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])->name('admin.events.edit');
        Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('admin.events.update');
        Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('admin.events.destroy');

        // Photo & Thumbnail Management
        Route::post('/events/{event}/photos', [AdminPhotoController::class, 'storeAdminPhoto'])->name('admin.events.photos.store');
        Route::post('/events/{event}/thumbnail', [AdminPhotoController::class, 'updateThumbnail'])->name('admin.events.thumbnail.update');
        Route::delete('/photos/{photo}', [AdminPhotoController::class, 'destroy'])->name('admin.photos.destroy');
    });
});
