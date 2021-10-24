<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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

// login Google
Route::get('/auth/google/redirect', [LoginController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

//SpreadSheet
Route::post('readSheet', [SpreadSheetController::class, 'readSheet']);

Route::post('addRowInSheet', [SpreadSheetController::class, 'addRowInSheet']);

Route::put('updateRowInSheet', [SpreadSheetController::class, 'updateRowInSheet']);

Route::get('testAuth', [SpreadSheetController::class, 'testAuth']);

// Route::get('/auth/google/redirect', function () {
//     return Socialite::driver('google')->redirect();
// });

// Route::get('/auth/google/callback', function () {
//     $user = Socialite::driver('google')->user();

//     // $user->token
// });


Route::get('/', function () {
    return view('welcome');
});

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
