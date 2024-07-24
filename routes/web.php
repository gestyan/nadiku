<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\PageController::class, 'index'])->name('home');

    Route::resource('user', \App\Http\Controllers\UserController::class)
        ->except(['show', 'edit', 'create'])
        ->middleware(['role:admin']);

    Route::get('profile', [\App\Http\Controllers\PageController::class, 'profile'])
        ->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\PageController::class, 'profileUpdate'])
        ->name('profile.update');
    Route::put('profile/deactivate', [\App\Http\Controllers\PageController::class, 'deactivate'])
        ->name('profile.deactivate')
        ->middleware(['role:staff']);

    Route::get('settings', [\App\Http\Controllers\PageController::class, 'settings'])
        ->name('settings.show')
        ->middleware(['role:admin']);
    Route::put('settings', [\App\Http\Controllers\PageController::class, 'settingsUpdate'])
        ->name('settings.update')
        ->middleware(['role:admin']);

    Route::delete('attachment', [\App\Http\Controllers\PageController::class, 'removeAttachment'])
        ->name('attachment.destroy');

  	Route::get('transaction/incoming/self', [\App\Http\Controllers\IncomingLetterController::class, 'self'])->name('transaction.incoming_self');
  	Route::get('transaction/outgoing/all', [\App\Http\Controllers\OutgoingLetterController::class, 'all'])->name('transaction.outgoing_all');
    Route::prefix('transaction')->as('transaction.')->group(function () {
        Route::resource('incoming', \App\Http\Controllers\IncomingLetterController::class);
        Route::resource('outgoing', \App\Http\Controllers\OutgoingLetterController::class);
        Route::put('outgoing/update_file/{id}', [\App\Http\Controllers\OutgoingLetterController::class, 'update_file'])->name('outgoing.update_file');
        Route::resource('{letter}/disposition', \App\Http\Controllers\DispositionController::class)->except(['show']);
    });

    Route::prefix('agenda')->as('agenda.')->group(function () {
        Route::get('incoming', [\App\Http\Controllers\IncomingLetterController::class, 'agenda'])->name('incoming');
        Route::get('incoming/print', [\App\Http\Controllers\IncomingLetterController::class, 'print'])->name('incoming.print');
        Route::get('outgoing', [\App\Http\Controllers\OutgoingLetterController::class, 'agenda'])->name('outgoing');
        Route::get('outgoing/list', [\App\Http\Controllers\OutgoingLetterController::class, 'list'])->name('outgoing.list');
        Route::get('outgoing/print', [\App\Http\Controllers\OutgoingLetterController::class, 'print'])->name('outgoing.print');
    });

    Route::prefix('gallery')->as('gallery.')->group(function () {
        Route::get('incoming', [\App\Http\Controllers\LetterGalleryController::class, 'incoming'])->name('incoming');
        Route::get('outgoing', [\App\Http\Controllers\LetterGalleryController::class, 'outgoing'])->name('outgoing');
    });

    Route::prefix('reference')->as('reference.')->middleware(['role:admin'])->group(function () {
        Route::resource('classification', \App\Http\Controllers\ClassificationController::class)->except(['show', 'create', 'edit']);
        Route::resource('status', \App\Http\Controllers\LetterStatusController::class)->except(['show', 'create', 'edit']);
    });

  	Route::get('template_letter', [\App\Http\Controllers\PageController::class, 'template'])->name('pages.template');
});

Route::controller(\App\Http\Controllers\GoogleController::class)->group(function(){
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback');
});

Route::get('/foo', function () {
    Artisan::call('storage:link');
});
