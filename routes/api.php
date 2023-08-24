<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    '/logout',
    [AuthController::class, 'logout']
);

// Route::group([
//     'middleware' => 'api',
//     'prefix' => 'auth'
// ], function ($router) {
//     Route::post('/login', [AuthController::class, 'login']);
//     Route::post('/register', [AuthController::class, 'register']);
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::post('/refresh', [AuthController::class, 'refresh']);
//     Route::get('/user-profile', [AuthController::class, 'userProfile']);
// });

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
