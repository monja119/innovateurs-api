<?php

use App\Http\Controllers\ForumController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessengerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthentificationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('webhook')->group(function () {
    Route::get('/', [MessengerController::class, 'verifyWebhook']);
    Route::post('/', [MessengerController::class, 'receiveMessage']);
});

Route::prefix('auth')->group(function () {
    Route::post('login/', [AuthentificationController::class, 'login']);
    Route::post('register/', [UserController::class, 'create']);
    Route::get('user/{token}', [UserController::class, 'show']);
});

Route::prefix("users")->group(function () {
    Route::get('', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::get('/{id}/activate', [UserController::class, 'activate']);
    Route::get('/{id}/deactivate', [UserController::class, 'deactivate']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::get('/{token}/listUsers', [UserController::class, 'tsyhaiko']);
// projects
Route::apiResource('projects', ProjectController::class);
Route::prefix('projects')->group(function () {
    Route::get('/user/{id}', [ProjectController::class, 'showUserProjects']);
});

// forums
Route::apiResource('forums', ForumController::class);
Route::prefix('forums')->group(function () {
    Route::get('/user/{id}', [ForumController::class, 'showUserForums']);
    Route::get('/type/{type}', [ForumController::class, 'filter']);
});

