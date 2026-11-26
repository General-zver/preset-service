<?php

use App\Http\Controllers\PresetController;
use App\Http\Controllers\ProductController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

    return response()->json([
        'token' => $user->createToken('vue')->plainTextToken,
    ]);
})->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::prefix('presets')->group(function () {
        Route::get('/', [PresetController::class, 'index']);
        Route::post('/', [PresetController::class, 'store']);
        Route::patch('/{preset}', [PresetController::class, 'update']);
        Route::delete('/{preset}', [PresetController::class, 'destroy']);
        Route::post('/{id}/book', [PresetController::class, 'book']);
    });
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::patch('/{product}', [ProductController::class, 'update']);
        Route::delete('/{product}', [ProductController::class, 'destroy']);
    });

    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    });
});
