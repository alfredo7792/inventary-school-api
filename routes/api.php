<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/category/list', [CategoryController::class, 'list']);
Route::get('/category/get', [CategoryController::class, 'getItem']);
Route::post('/category/create', [CategoryController::class, 'create']);
Route::post('/category/update', [CategoryController::class, 'update']);
Route::delete('/category/delete', [CategoryController::class, 'delete']);