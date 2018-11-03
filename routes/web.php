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

Route::get('/', function () {
    return view('welcome');
});
//用户端
Route::post('user/login','User\UserController@login');
//商家列表
Route::get('shop/list','User\ShopController@list');
Route::get('shop/shoplist','User\ShopController@shoplist');

//短信
Route::get('user/sendSms','User\UserController@sendSms');

//会员注册
Route::post('user/regist','User\UserController@regist');

//地址列表
Route::get('shop/addressList','User\ShopController@addressList');

//添加地址
Route::post('shop/addAddress','User\ShopController@addAddress');
//指定地址
Route::get('shop/address','User\ShopController@address');
//修改地址
Route::post('shop/editAddress','User\ShopController@editAddress');

//获取购物车
Route::get('shop/cart','User\ShopController@cart');
//保存购物车
Route::post('shop/addCart','User\ShopController@addCart');

// 添加订单接口
Route::post('shop/addorder','User\ShopController@addorder');
// 获得订单列表接口
Route::post('shop/orderList','User\ShopController@orderList');
//获得指定订单接口
Route::get('shop/order','User\ShopController@order');
//修改密码
Route::post('shop/changePassword','User\ShopController@changePassword');
//忘记密码
Route::post('shop/forgetPassword','User\ShopController@forgetPassword');


