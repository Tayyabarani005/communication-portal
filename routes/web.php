<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\DirectMessageController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PinnedMessageController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('workspaces.index')
        : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    /* ── Workspaces ── */
    Route::resource('workspaces', WorkspaceController::class);

    Route::prefix('workspaces/{workspace}')->group(function () {
        Route::get('channels/create', [ChannelController::class, 'create'])->name('workspaces.channels.create');
        Route::post('channels', [ChannelController::class, 'store'])->name('workspaces.channels.store');
        Route::post('channels/{channel}/join', [ChannelController::class, 'join'])->name('channels.join');
        Route::post('channels/{channel}/leave', [ChannelController::class, 'leave'])->name('channels.leave');
    });

    /* ── Channels ── */
    Route::get('channels/{channel}', [ChannelController::class, 'show'])->name('channels.show');
    Route::get('channels/{channel}/edit', [ChannelController::class, 'edit'])->name('channels.edit');
    Route::patch('channels/{channel}', [ChannelController::class, 'update'])->name('channels.update');
    Route::delete('channels/{channel}', [ChannelController::class, 'destroy'])->name('channels.destroy');

    Route::prefix('channels/{channel}')->group(function () {
        Route::post('messages/{message}/pin', [PinnedMessageController::class, 'store'])->name('messages.pin');
        Route::delete('pins/{pin}', [PinnedMessageController::class, 'destroy'])->name('messages.unpin');
        Route::get('tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::patch('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    });

    /* ── Direct Messages ── */
    Route::get('dms', [DirectMessageController::class, 'index'])->name('dms.index');
    Route::get('dms/{conversation}', [DirectMessageController::class, 'show'])->name('dms.show');
    Route::post('dms/start/{user}', [DirectMessageController::class, 'start'])->name('dms.start');

    /* ── Files ── */
    Route::post('files', [FileController::class, 'store'])->name('files.store');
    Route::get('files/{file}/download', [FileController::class, 'download'])->name('files.download');

    /* ── Profile ── */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
