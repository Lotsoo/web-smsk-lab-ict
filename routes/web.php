<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\SuratKeluarController;
use App\Http\Controllers\Dashboard\SuratMasukController;
use App\Http\Controllers\Dashboard\SuratRevisiController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Route::get('/check-auth', function () {
//     return auth()->check() ? 'Logged In' : 'Not Logged In';
// });

Route::post('/login', [LoginController::class, 'authenticate']);
Route::get('/login', [LoginController::class, 'index'])->name('login');

Route::middleware(['auth'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('surat-masuk')->group(function () {
            Route::get('/', [SuratMasukController::class, 'index'])->name('surat-masuk');
            Route::post('/', [SuratMasukController::class, 'store'])->name('surat-masuk.store');
            Route::get('/{id}', [SuratMasukController::class, 'show'])->name('surat-masuk.show');
            Route::get('/{id}/edit', [SuratMasukController::class, 'edit'])->name('surat-masuk.edit')->middleware('role:superadmin,admin');
            Route::get('/{id}/review', [SuratMasukController::class, 'review'])->name('surat-masuk.review')->middleware('role:superadmin');
            Route::post('/{id}/review', [SuratMasukController::class, 'submitReview'])->name('surat-masuk.submit-review')->middleware('role:superadmin');
            Route::put('/{id}', [SuratMasukController::class, 'update'])->name('surat-masuk.update');
            Route::delete('/{id}', [SuratMasukController::class, 'destroy'])->name('surat-masuk.destroy');
            Route::get('/{id}/revision', [SuratMasukController::class, 'revision'])->name('surat-masuk.revision');
            Route::post('/{id}/revision', [SuratMasukController::class, 'processRevision'])->name('surat-masuk.process-revision')->middleware('role:superadmin');
            Route::post('/{id}/disposisi', [SuratMasukController::class, 'disposisi'])->name('surat-masuk.disposisi')->middleware('role:superadmin');
            Route::patch('/{id}/update-file', [SuratMasukController::class, 'updateFile'])->name('surat-masuk.update-file');
            Route::get('/{id}/download', [SuratMasukController::class, 'downloadFile'])->name('surat-masuk.download');
            Route::get('/{id}/view', [SuratMasukController::class, 'viewFile'])->name('surat-masuk.view');
        });

        Route::prefix('surat-revisi')->group(function () {
            Route::get('/', [SuratRevisiController::class, 'index'])->name('surat-revisi');
            Route::get('/{id}/edit', [SuratRevisiController::class, 'edit'])->name('surat-revisi.edit');
            Route::patch('/{id}', [SuratRevisiController::class, 'update'])->name('surat-revisi.update');
        });

        Route::prefix('surat-keluar')->group(function () {
            Route::get('/', [SuratKeluarController::class, 'index'])->name('surat-keluar');
            Route::post('/store', [SuratKeluarController::class, 'store'])->name('surat-keluar.store');
            Route::get('/{id}/edit', [SuratKeluarController::class, 'edit'])->name('surat-keluar.edit');
            Route::patch('/{id}', [SuratKeluarController::class, 'update'])->name('surat-keluar.update');
            Route::get('/{id}', [SuratKeluarController::class, 'show'])->name('surat-keluar.show');
            Route::delete('/{id}', [SuratKeluarController::class, 'destroy'])->name('surat-keluar.destroy');
            Route::get('/{id}/review', [SuratKeluarController::class, 'review'])->name('surat-keluar.review')->middleware('role:superadmin');
            Route::get('/{id}/print', [SuratKeluarController::class, 'cetak'])->name('surat-keluar.print');
            Route::patch('/{id}/approval', [SuratKeluarController::class, 'approval'])->name('surat-keluar.approval')->middleware('role:superadmin');
            Route::post('/{id}/comment', [SuratKeluarController::class, 'tambahKomentar'])->name('surat-keluar.comment')->middleware('role:superadmin');
        });
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::get("/tester", [LoginController::class, "registrasi"]);
