<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\VideoController;

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

Route::middleware('api')->group(function () {
    // Profile routes
    Route::apiResource('profiles', ProfileController::class);
    
    // Video routes
    Route::apiResource('videos', VideoController::class);
    Route::get('videos/featured', [VideoController::class, 'featured']);
    Route::get('profiles/{profile}/videos', [VideoController::class, 'byProfile']);
    
    // Health check route
    Route::get('/health', function () {
        return response()->json(['status' => 'ok', 'timestamp' => now()]);
    });
});