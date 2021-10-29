<?php

use Illuminate\Support\Facades\Route;

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

//dashboard
Route::get('/dashboard',[LoginController::class, 'check'])->name('dashboard');

Route::get('/spreadsheets', [SpreadSheetController::class, 'addSheet'])->name("spreadsheets");
// Route::post('/create', [LoginController::class, 'redirectToGoogle'])->name("create");

//SpreadSheet
Route::post('readSheet', [SpreadSheetController::class, 'readSheet'])->name("readSheet");

Route::post('addRowInSheet', [SpreadSheetController::class, 'addRowInSheet']);

Route::put('updateRowInSheet', [SpreadSheetController::class, 'updateRowInSheet']);


// Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
