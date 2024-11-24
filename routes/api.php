<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\JWTMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'loginAdmin']);

Route::middleware([JWTMiddleware::class])->group(function () {
    Route::get('/prueba', [CategoryController::class, 'prueba']);
});


Route::get('/category/list', [CategoryController::class, 'list']);
Route::get('/category/get', [CategoryController::class, 'getItem']);
Route::post('/category/create', [CategoryController::class, 'create']);
Route::post('/category/update', [CategoryController::class, 'update']);
Route::delete('/category/delete', [CategoryController::class, 'delete']);

Route::get('/material/list', [MaterialController::class, 'list']);
Route::get('/material/get', [MaterialController::class, 'getItem']);
Route::post('/material/create', [MaterialController::class, 'create']);
Route::post('/material/update', [MaterialController::class, 'update']);
Route::delete('/material/delete', [MaterialController::class, 'delete']);
Route::get('/material/report', [MaterialController::class, 'getReport']);

Route::get('/user/list', [UserController::class, 'list']);
Route::get('/user/get', [UserController::class, 'getItem']);
Route::post('/user/create', [UserController::class, 'create']);
Route::post('/user/update', [UserController::class, 'update']);
Route::delete('/user/delete', [UserController::class, 'delete']);

Route::get('/role/list', [UserController::class, 'listRoles']);

Route::get('/movement/list', [MovementController::class, 'list']);
Route::get('/movement/get', [MovementController::class, 'getItem']);
Route::post('/movement/create', [MovementController::class, 'create']);
Route::post('/movement/update', [MovementController::class, 'update']);
Route::delete('/movement/delete', [MovementController::class, 'delete']);
Route::get('/movement/report', [MovementController::class, 'getReport']);
Route::get('/movement/imprimir', [MovementController::class, 'imprimir']);