<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\SpreadSheetController;
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

Route::get('/', function () {
    return view('home', ['name' => 'Home']);
});

// Auth

// login Google
Route::get('/auth/google/redirect', [LoginController::class, 'redirectToGoogle'])->name('login');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

//dashboard
Route::get('/dashboard' ,function () {
    return view('dashboard.home');
});

Route::get('/spreadsheets', [SpreadSheetController::class, 'addSheet'])->name("spreadsheets");
// Route::post('/create', [LoginController::class, 'redirectToGoogle'])->name("create");

//SpreadSheet
Route::post('readSheet', [SpreadSheetController::class, 'readSheet'])->name("readSheet");

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




// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
