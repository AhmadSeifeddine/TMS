<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::get('tasks/{task}/comments', [TaskController::class, 'getComments'])->name('tasks.comments');
});

require __DIR__.'/auth.php';
