<?php

use App\Http\Controllers\API\V1\ApiAuthController;
use App\Http\Controllers\API\V1\PostController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:api')->group(function () {
    Route::get('all-posts', [PostController::class, 'getAllPosts']);
    Route::post('create-post', [PostController::class, 'createPost']);
    Route::delete('delete-post/{id}', [PostController::class, 'deletePost']);
    route::patch('set-post-status', [PostController::class, 'changePostStatus']);
    Route::post('search-post', [PostController::class, 'searchPost']);

    Route::post('logout', [ApiAuthController::class, 'logout']);
});

Route::post('register', [ApiAuthController::class, 'register']);
Route::post('login', [ApiAuthController::class, 'login']);
