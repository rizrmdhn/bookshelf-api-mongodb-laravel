<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ImagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post(
    '/login',
    [AuthController::class, 'login']
);

Route::post(
    '/register',
    [AuthController::class, 'register']
);

Route::post(
    '/avatar',
    [AuthController::class, 'updateAvatar']
);

Route::get(
    '/me',
    [AuthController::class, 'userProfile']
);

Route::post(
    '/logout',
    [AuthController::class, 'logout']
);

Route::get(
    '/books',
    [BookController::class, 'all']
);
Route::get(
    '/books/{id}',
    [BookController::class, 'show']
);
Route::post(
    '/books',
    [BookController::class, 'store']
);
Route::delete(
    '/books/{id}',
    [BookController::class, 'destroy']
);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
