<?php

use Config\Router;

Router::get('/', 'HomeController@index');

//Connexion
Router::get('/login', 'AuthController@display');
Router::get('/logout', 'AuthController@logOut', 'protected');
Router::get('/reset/password', 'AuthController@forgetPasswordFrom');
Router::get('/reset-password/{token}', 'AuthController@showResetForm');
Router::get('/dashboard', 'DashboardController@show', 'protected');
Router::get('/reservations/create', 'ReservationController@create', 'protected');
Router::get('/reservations', 'ReservationController@index', 'protected');
Router::get('/admin/materials', 'MaterialController@show', 'protected');
Router::get('/admin/users', 'UserController@show', 'protected');






// POST && API 
Router::post('/api/captcha', 'CaptchaController@validate');
Router::post('/login', 'AuthController@login');
Router::post('/forgot-password', 'AuthController@forgotPassword');
Router::post('/reset-password', 'AuthController@resetPassword');
Router::get('/reservations/search/{search}', 'ReservationController@search', 'protected');
Router::post('/materials/create', 'MaterialController@create', 'protected');
Router::post('/materials/update/{id}', 'MaterialController@update', 'protected');
Router::post('/materials/delete/{id}', 'MaterialController@delete', 'protected');

Router::post('/reservations/create', 'ReservationController@create', 'protected');
Router::post('/reservations/update/{id}', 'ReservationController@update', 'protected');
Router::post('/reservations/delete/{id}', 'ReservationController@delete', 'protected');

Router::post('/admin/users/create', 'UserController@create', 'protected');
Router::post('/admin/users/update/{id}', 'UserController@update', 'protected');
Router::post('/admin/users/delete/{id}', 'UserController@delete', 'protected');

Router::dispatch();
