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
// OTP
Route::get('/verifikasi-otp','HomeController@verifiedotp')->name('verifiedotp');
Route::post('/verifikasi-otp','HomeController@verifiedotpcheck')->name('verifiedotp');
Route::get('/otp-verify/{no_telp}','HomeController@verifyotp')->name('verifyotp');
Route::post('/otp-verify','HomeController@verified')->name('verified');
Route::get('/generate-otp','HomeController@otp')->name('generateotp');
Route::post('/generate-otp','HomeController@generateotp')->name('generateotp');


Route::get('/', function () {
    return view('auth.register');
});

Auth::routes();
Route::get('/home', 'HomeController@index')->middleware('auth')->name('home');

