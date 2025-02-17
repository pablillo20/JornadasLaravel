<?php

use App\Http\Controllers\PonenteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/ponentes', function () {
    return view('ponentes');
})->middleware(['auth', 'verified', 'admin'])->name('ponentes');

Route::get('/eventos', function () {
    return view('eventos');
})->middleware(['auth', 'verified', 'admin'])->name('eventos');

Route::get('/pagos', function () {
    return view('pagos');
})->middleware(['auth', 'verified', 'admin'])->name('pagos');

Route::get('/usuarios', function () {
    return view('usuarios');
})->middleware(['auth', 'verified', 'admin'])->name('usuarios');

Route::get('/EventosUser', function () {
    return view('eventosUsuarios');  // Asegúrate que el nombre del archivo de vista sea 'eventosUsuarios.blade.php'
})->middleware(['auth', 'verified'])->name('EventosUser');

Route::get('/ponentesUser', function () {
    return view('ponentesUsuarios');
})->middleware(['auth', 'verified', 'notAdmin'])->name('ponentesUser');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
