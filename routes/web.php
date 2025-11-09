<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

use App\Livewire\TransactionsIndex;
use App\Livewire\TransactionForm;
use App\Livewire\TransactionDetail;

/*
|---------------------------------------------------------------------------
| Serve file dari disk public tanpa controller (/media/{path})
| Contoh: /media/covers/nama-file.jpg
|---------------------------------------------------------------------------
*/
Route::get('/media/{path}', function (string $path) {
    $path = urldecode($path);
    $path = ltrim(str_replace('\\', '/', $path), '/');
    $path = preg_replace('#^(public/|storage/)#', '', $path);

    $disk = Storage::disk('public');
    if (!$disk->exists($path)) {
        abort(404, 'File not found');
    }

    $mime = $disk->mimeType($path) ?? 'application/octet-stream';
    return Response::make($disk->get($path), 200, [
        'Content-Type'  => $mime,
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ]);
})->where('path', '.*')->name('media');

/*
|---------------------------------------------------------------------------
| Auth
|---------------------------------------------------------------------------
*/
Route::group(['prefix' => 'auth'], function () {
    Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

/*
|---------------------------------------------------------------------------
| App (protected)
|---------------------------------------------------------------------------
*/
Route::group(['prefix' => 'app', 'middleware' => 'check.auth'], function () {
    Route::get('/home', fn() => redirect()->route('app.transactions.index'))->name('app.home');

    Route::get('/transactions', TransactionsIndex::class)->name('app.transactions.index');
    Route::get('/transactions/create', TransactionForm::class)->name('app.transactions.create');
    Route::get('/transactions/{id}/edit', TransactionForm::class)->name('app.transactions.edit');
    Route::get('/transactions/{id}', TransactionDetail::class)->name('app.transactions.show');
});

/*
|---------------------------------------------------------------------------
| Root
|---------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('app.transactions.index'));
