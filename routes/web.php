<?php

use App\Http\Controllers\Dashboard\Admin\ProfileDosenController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPage\ActivityController;
use Inertia\Inertia;

/* Landing Page Routes (Blade Views) */

Route::get('/', function () {
    return view('pages.landing');
})->name('landing');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/lecturers', function () {
    return view('pages.lecturer.lecturers');
})->name('lecturers');



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
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/dashboard');
    })->name('admin.dashboard');

    Route::get('/dosen', function () {
        return Inertia::render('Admin/dosen');
    })->name('admin.dosen');

    // Profile Dosen Routes
    Route::get('/profile-dosen', [ProfileDosenController::class, 'index'])->name('admin.profiledosen');
    Route::post('/profile-dosen', [ProfileDosenController::class, 'store'])->name('admin.profiledosen.store');
    Route::put('/profile-dosen/{id}', [ProfileDosenController::class, 'update'])->name('admin.profiledosen.update');
    Route::delete('/profile-dosen/{id}', [ProfileDosenController::class, 'destroy'])->name('admin.profiledosen.destroy');

    Route::get('/aktivitas', function () {
        return Inertia::render('Admin/aktivitasdosen');
    })->name('admin.aktivitasdosen');
});



/* Dosen Panel Routes (Inertia Vue) */

Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->group(function () {
    
    Route::get('/dashboard', function () {
        return Inertia::render('Dosen/dashboard');
    })->name('dosen.dashboard');

    Route::get('/aktivitas', function () {
        return Inertia::render('Dosen/aktivitas');
    })->name('dosen.aktivitas');

    Route::get('/profile', function () {
        return Inertia::render('Dosen/profile');
    })->name('dosen.profile');

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
