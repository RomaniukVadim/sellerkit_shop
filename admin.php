<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "Admin" middleware group. Enjoy building your Admin!
|
*/

Route::match(['get', 'post'],'/login', 'Admin@login');
Route::get('/', 'Admin@index')->middleware('Login');
Route::get('/logout', 'Admin@logout')->middleware('Login');
Route::get('/map', 'Admin@map')->middleware('Login');
Route::match(['get', 'post'],'/products', 'Admin@products')->middleware('Login');
Route::match(['get', 'post'],'/categories', 'Admin@categories')->middleware('Login');
Route::match(['get', 'post'],'/pages', 'Admin@pages')->middleware('Login');
Route::match(['get', 'post'],'/blog', 'Admin@blog')->middleware('Login');
Route::get('/customers', 'Admin@customers')->middleware('Login');
Route::match(['get', 'post'],'/coupons', 'Admin@coupons')->middleware('Login');
Route::match(['get', 'post'],'/shipping', 'Admin@shipping')->middleware('Login');
Route::match(['get', 'post'],'/reviews', 'Admin@reviews')->middleware('Login');
Route::match(['get', 'post'],'/orders', 'Admin@orders')->middleware('Login');
Route::get('/stats', 'Admin@stats')->middleware('Login');
Route::match(['get', 'post'],'/tracking', 'Admin@tracking')->middleware('Login');
Route::match(['get', 'post'],'/newsletter', 'Admin@newsletter')->middleware('Login');
Route::get('/referrers', 'Admin@referrers')->middleware('Login');
Route::get('/os', 'Admin@os')->middleware('Login');
Route::get('/browsers', 'Admin@browsers')->middleware('Login');
Route::match(['get', 'post'],'/payment', 'Admin@payment')->middleware('Login');
Route::match(['get', 'post'],'/currency', 'Admin@currency')->middleware('Login');
Route::match(['get', 'post'],'/settings', 'Admin@settings')->middleware('Login');
Route::match(['get', 'post'],'/theme', 'Admin@theme')->middleware('Login');
Route::match(['get', 'post'],'/lang', 'Admin@lang')->middleware('Login');
Route::get('/tokens', 'Admin@tokens')->middleware('Login');
Route::get('/export', 'Admin@export')->middleware('Login');
Route::match(['get', 'post'],'/editor', 'Admin@editor')->middleware('Login');
Route::match(['get', 'post'],'/templates', 'Admin@templates')->middleware('Login');
Route::match(['get', 'post'],'/builder', 'Admin@builder')->middleware('Login');
Route::match(['get', 'post'],'/menu', 'Admin@menu')->middleware('Login');
Route::match(['get', 'post'],'/bottom', 'Admin@bottom')->middleware('Login');
Route::match(['get', 'post'],'/fields', 'Admin@fields')->middleware('Login');
Route::match(['get', 'post'],'/support', 'Admin@support')->middleware('Login');
Route::match(['get', 'post'],'/administrators', 'Admin@administrators')->middleware('Login');
Route::match(['get', 'post'],'/profile', 'Admin@profile')->middleware('Login');
