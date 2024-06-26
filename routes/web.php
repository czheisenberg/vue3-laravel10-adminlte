<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/admin/dashboard',function(){
//     return view('dashboard');
// });

Route::get('/api/users/', [UserController::class, 'index']);
Route::post('/api/users/', [UserController::class, 'store']);
Route::patch('/api/users/{user}/change-role',[UserController::class, 'changeRole']);
Route::put('/api/users/{user}', [UserController::class, 'update']);
Route::delete('/api/users/{user}', [UserController::class, 'destory']);
Route::get('/api/users/search', [UserController::class, 'search']);

// 改为vue3的路由
Route::get('{view}', ApplicationController::class)->where('view','(.*)');