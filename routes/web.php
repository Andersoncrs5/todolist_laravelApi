<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::resource('task', TaskController::class);
});
