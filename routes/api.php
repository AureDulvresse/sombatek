<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductReviewController;

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

Route::middleware('auth:sanctum')->group(function () {
   Route::post('/product-reviews', [ProductReviewController::class, 'store']);
   Route::put('/product-reviews/{review}', [ProductReviewController::class, 'update']);
   Route::delete('/product-reviews/{review}', [ProductReviewController::class, 'destroy']);
});

Route::middleware([
   'auth:sanctum',
   config('jetstream.auth_middleware')
])->group(function () {
   Route::get('/user', function (Request $request) {
      return $request->user();
   });
});

Route::middleware([
   'auth:sanctum',
   config('jetstream.auth_middleware')
])->group(function () {
   Route::get('/user', function (Request $request) {
      return $request->user();
   });
});
