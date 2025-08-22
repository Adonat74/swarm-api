<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Example test route
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});
