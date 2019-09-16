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

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');
Route::get('signup','UsersController@create')->name('signup');

//用户模块
//以下用户路由等同于
//Route::resource('users', 'UsersController');

//显示所有用户列表
Route::get('/users', 'UsersController@index')->name('users.index');
//创建用户页面
Route::get('/users/create', 'UsersController@create')->name('users.create');
//显示用户个人信息页面
Route::get('/users/{user}', 'UsersController@show')->name('users.show');
//创建用户
Route::post('/users', 'UsersController@store')->name('users.store');
//编辑用户个人信息资料
Route::get('/users/{user}/edit', 'UsersController@edit')->name('users.edit');
//更新用户
Route::patch('/users/{user}', 'UsersController@update')->name('users.update');
//删除用户
Route::delete('/users/{user}', 'UsersController@destroy')->name('users.destroy');

//登录登出模块
//显示登录页面
Route::get('login', 'SessionsController@create')->name('login');
//创建新会话（登录）
Route::post('login', 'SessionsController@store')->name('login');
//销毁会话（退出登录）
Route::delete('logout', 'SessionsController@destroy')->name('logout');

//用户认证邮件发送
Route::get('signup/confirm/{token}','UsersController@confirmEmail')->name('confirm_email');

//密码重置模块
//显示重置密码的邮箱发送页面
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
//邮箱发送重设链接
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
//密码更新页面
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
//执行密码更新操作
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

//微博相关操作
//store处理创建微博的请求，destroy处理删除微博的请求
Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);

//粉丝关系相关操作
//显示用户的关注人列表
Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');
//显示用户的粉丝列表
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');
//关注用户逻辑
Route::post('/users/followers/{user}', 'FollowersController@store')->name('followers.store');
//取消关注用户逻辑
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');
