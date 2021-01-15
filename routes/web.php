<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\PasswordResetController;


use Arcanedev\NoCaptcha\Rules\CaptchaRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/profile', [App\Http\Controllers\HomeController::class, 'profile'])->name('profile');
Route::post('/updateprofile',[App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateprofile');

Route::post('create', [PasswordResetController::class,'create'])->name('create');
Route::get('find/{email}/{token}', [PasswordResetController::class,'find']);
Route::post('reset', [PasswordResetController::class,'reset'])->name('reset');



