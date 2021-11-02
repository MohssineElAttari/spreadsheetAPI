<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\SpreadSheetController;
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

Route::get('/logout', function () {
    auth()->logout();
    return redirect('/');
});


// Auth::routes();

Route::get('/', function () {
    return view('home');
});

Route::group(['middleware' => 'web'], function () {

    // Auth::routes();

    //dashboard
    Route::get('/dashboard', function () {
        return view('dashboard.home');
    })->name('dashboard');
    Route::get('/create', [SpreadSheetController::class, 'addSheet'])->name("create");
    // Route::post('/create', [LoginController::class, 'redirectToGoogle'])->name("create");

    //SpreadSheet
    Route::post('readSheet', [SpreadSheetController::class, 'readSheet'])->name("readSheet");

    Route::post('addRowInSheet', [SpreadSheetController::class, 'addRowInSheet']);

    Route::put('updateRowInSheet', [SpreadSheetController::class, 'updateRowInSheet']);

    Route::Post('createApi', [SpreadSheetController::class, 'createApi'])->name('createApi');

    Route::get('spreadsheets', [SpreadSheetController::class, 'ShowpreadSheet'])->name('show');
});
