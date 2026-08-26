<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
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

Route::get('/activities', function () {
    return view('pages.activity.activities');
})->name('activities');

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
    })->name('dashboard');
    
    Route::get('/admin/dashboard', function () {
        return Inertia::render('Admin/dashboard');
    })->name('admin.dashboard');
    
    Route::get('/dosen', function () {
        return Inertia::render('Admin/dosen');
    })->name('dosen');
    
    Route::get('/admin/dosen', function () {
        return Inertia::render('Admin/dosen');
    })->name('admin.dosen');
    
    Route::get('/profile-dosen', function () {
        return Inertia::render('Admin/profiledosen');
    })->name('profiledosen');
    
    Route::get('/admin/profile-dosen', function () {
        return Inertia::render('Admin/profiledosen');
    })->name('admin.profiledosen');
    
    Route::get('/aktivitas', function () {
        return Inertia::render('Admin/aktivitasdosen');
    })->name('aktivitasdosen');
    
    Route::get('/admin/aktivitas', function () {
        return Inertia::render('Admin/aktivitasdosen');
    })->name('admin.aktivitasdosen');

});



/* Dosen Panel Routes (Inertia Vue) */

Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->group(function () {
    
    Route::get('/dashboard', function () {
        return Inertia::render('Dosen/dashboard');
    })->name('dosen.dashboard');

    Route::get('/aktivitas', function () {
        return Inertia::render('Dosen/aktivitasdosen');
    })->name('dosen.aktivitas');

    Route::get('/profile', function () {
        return Inertia::render('Dosen/profiledosen');
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


Route::get('/activity/{slug}', function ($slug) {
    return view('pages.activity.show', compact('slug'));
})->where('slug', '^(?!dashboard|aktivitas|profile).*$')->name('activity.show');
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
