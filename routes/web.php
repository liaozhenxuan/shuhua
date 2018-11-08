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

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/','IndexController@index');
Route::get('/index','IndexController@index');

//个人简介
Route::get('/author','IndexController@author');
Route::get('/author/gry','IndexController@author_gry');
Route::get('/author/gry_show','IndexController@author_gry_show');

//作品
Route::get('/product','IndexController@product');
Route::get('/product/show','IndexController@product_show');

//新闻、常识
Route::get('/news','IndexController@news');
Route::get('/common_sense','IndexController@news');
Route::get('/news/show','IndexController@news_show');