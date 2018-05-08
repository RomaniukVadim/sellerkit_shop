<?php

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
Route::get('/', 'Frontend@index');
Route::match(['get', 'post'],'/register', 'Frontend@register');
Route::match(['get', 'post'],'/login', 'Frontend@login');
Route::get('/account', 'Frontend@account')->middleware('Customer');
Route::match(['get', 'post'],'/profile', 'Frontend@profile')->middleware('Customer');
Route::get('/invoice/{order}', 'Frontend@invoice')->middleware('Customer');
Route::get('/logout', 'Frontend@logout')->middleware('Customer');
Route::get('/cart', 'Frontend@cart');
Route::get('/language/{language_code}', 'Frontend@language');
Route::get('/currency/{currency_code}', 'Frontend@currency');
Route::get('/blog', 'Frontend@blog');
Route::get('/blog/{post}', 'Frontend@post');
Route::get('/page/{page}', 'Frontend@page');
Route::get('/products', 'Frontend@products');
Route::get('/products/{category}', 'Frontend@products');
Route::get('/product/{product}', 'Frontend@product');
Route::match(['get', 'post'],'/support', 'Frontend@support');
Route::get('/support/map', 'Frontend@map');
Route::get('/success', 'Frontend@success');
Route::get('/failed', 'Frontend@failed');
// Installer routes
Route::get('/install', 'Installer@requirements');
Route::match(['get', 'post'],'/install/database', 'Installer@database');
Route::match(['get', 'post'],'/install/configurations', 'Installer@configurations');
Route::get('/install/success', 'Installer@success');
