<?php

use App\Http\Controllers\ContentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\UserDetail;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('details');
});

Route::get('/user-details/countries', function () {
    $countries = UserDetail::select('country')
        ->whereNotNull('country')
        ->distinct()
        ->orderBy('country')
        ->pluck('country');

    return response()->json($countries);
});

Route::get('/contents', [ContentController::class, 'index']);
Route::get('/featured-contents', [ContentController::class, 'featured']);
Route::post('/contents', [ContentController::class, 'store']);
Route::put('/contents/{id}', [ContentController::class, 'update']);
Route::get('/contents/{id}', [ContentController::class, 'show']);
Route::delete('/contents/{id}', [ContentController::class, 'destroy']);

