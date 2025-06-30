<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Project routes
    Route::resource('projects', ProjectController::class);

    // Project team management routes
    Route::get('projects/{project}/team-data', [ProjectController::class, 'getTeamData'])->name('projects.team-data');
    Route::post('projects/{project}/assign-members', [ProjectController::class, 'assignMembers'])->name('projects.assign-members');
    Route::delete('projects/{project}/remove-member', [ProjectController::class, 'removeMember'])->name('projects.remove-member');

    // Task routes
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('tasks/{task}/comments', [TaskController::class, 'getComments'])->name('tasks.comments');
    Route::post('task-comments', [TaskController::class, 'storeComment'])->name('task-comments.store');

    // Gallery routes
    Route::get('gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::post('gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::delete('gallery/{gallery}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
    Route::post('gallery/create-zip', [GalleryController::class, 'createZip'])->name('gallery.create-zip');
    Route::get('gallery/zip-status/{jobId}', [GalleryController::class, 'checkZipStatus'])->name('gallery.zip-status');
    Route::get('gallery/download-zip/{filename}', [GalleryController::class, 'downloadZip'])->name('gallery.download-zip');
});

require __DIR__.'/auth.php';
