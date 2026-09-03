<?php

use App\Http\Controllers\Dashboard\Admin\AktivitasDosenController;
use App\Http\Controllers\Dashboard\Admin\DashboardController;
use App\Http\Controllers\Dashboard\Admin\DosenController;
use App\Http\Controllers\Dashboard\Admin\ProfileDosenController;
use App\Http\Controllers\Dashboard\Admin\ScholarController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPage\ActivityController;
use App\Http\Controllers\LandingPage\LandingProfileController;
use App\Http\Controllers\LandingPage\LandingController;
use Inertia\Inertia;

/* Landing Page Routes (Blade Views) */

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/lecturers', [LandingProfileController::class, 'index'])->name('lecturers');

Route::get('/lecturers/{id}', [LandingProfileController::class, 'show'])->name('lecturer.show');

Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

Route::get('/activities/{id}', [ActivityController::class, 'show'])->name('activity.show');

/* Route Auth */

Route::get('/login', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/* Admin Panel Routes (Inertia Vue) */

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Dosen Account Routes (CRUD User role: dosen)
    Route::get('/dosen', [DosenController::class, 'index'])->name('admin.dosen');
    Route::post('/dosen', [DosenController::class, 'store'])->name('admin.dosen.store');
    Route::put('/dosen/{id}', [DosenController::class, 'update'])->name('admin.dosen.update');
    Route::delete('/dosen/{id}', [DosenController::class, 'destroy'])->name('admin.dosen.destroy');
    Route::post('/dosen/import', [DosenController::class, 'import'])->name('admin.dosen.import');

    // Profile Dosen Routes
    Route::get('/profile-dosen', [ProfileDosenController::class, 'index'])->name('admin.profiledosen');
    Route::post('/profile-dosen', [ProfileDosenController::class, 'store'])->name('admin.profiledosen.store');
    Route::put('/profile-dosen/{id}', [ProfileDosenController::class, 'update'])->name('admin.profiledosen.update');
    Route::delete('/profile-dosen/{id}', [ProfileDosenController::class, 'destroy'])->name('admin.profiledosen.destroy');

    // Aktivitas Dosen Routes
    Route::get('/aktivitas', [AktivitasDosenController::class, 'index'])->name('admin.aktivitasdosen');
    Route::post('/aktivitas', [AktivitasDosenController::class, 'store'])->name('admin.aktivitasdosen.store');
    Route::post('/aktivitas/{id}', [AktivitasDosenController::class, 'update'])->name('admin.aktivitasdosen.update');
    Route::delete('/aktivitas/{id}', [AktivitasDosenController::class, 'destroy'])->name('admin.aktivitasdosen.destroy');
    
    // Scholar Sync Route
    Route::get('/scholar', [ScholarController::class, 'index'])->name('admin.scholar');
    Route::post('/scholar/run', [ScholarController::class, 'runScraper'])->name('admin.scholar.sync');

});



/* Dosen Panel Routes (Inertia Vue) */

Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->group(function () {
    // Rute Profile Dosen (Selalu dapat diakses)
    Route::get('/profile', [\App\Http\Controllers\Dashboard\Dosen\DosenProfileController::class, 'index'])->name('dosen.profile');
    Route::post('/profile/personal', [\App\Http\Controllers\Dashboard\Dosen\DosenProfileController::class, 'updatePersonal'])->name('dosen.profile.personal');
    Route::post('/profile/account', [\App\Http\Controllers\Dashboard\Dosen\DosenProfileController::class, 'updateAccount'])->name('dosen.profile.account');
    Route::post('/profile/password', [\App\Http\Controllers\Dashboard\Dosen\DosenProfileController::class, 'updatePassword'])->name('dosen.profile.password');

    // Rute yang Dilindungi (Wajib ganti password default terlebih dahulu)
    Route::middleware('dosen.password_changed')->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Dosen/dashboard');
        })->name('dosen.dashboard');

        Route::get('/aktivitas', [\App\Http\Controllers\Dashboard\Dosen\DosenAktivitasController::class, 'index'])->name('dosen.aktivitas');
        Route::post('/aktivitas', [\App\Http\Controllers\Dashboard\Dosen\DosenAktivitasController::class, 'store'])->name('dosen.aktivitas.store');
        Route::post('/aktivitas/{id}', [\App\Http\Controllers\Dashboard\Dosen\DosenAktivitasController::class, 'update'])->name('dosen.aktivitas.update');
        Route::delete('/aktivitas/{id}', [\App\Http\Controllers\Dashboard\Dosen\DosenAktivitasController::class, 'destroy'])->name('dosen.aktivitas.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Public Lecturer Detail Route (Blade)
| Note: Must be placed AFTER /dosen/... static routes with regex guard
|--------------------------------------------------------------------------
*/

Route::get('/dosen/{slug}', function ($slug) {
    return view('pages.lecturer.show', compact('slug'));
})->where('slug', '^(?!dashboard|aktivitas|profile).*$')->name('lecturer.show');



/*
|--------------------------------------------------------------------------
| Authenticated Profile Routes (Gatau apaan kaga dipake kayaknya)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
