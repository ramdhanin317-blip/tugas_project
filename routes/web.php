<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return redirect('/categories');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('categories', CategoryController::class);
});