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







// POST && API 
Router::post('/api/captcha', 'CaptchaController@validate');
Router::post('/login', 'AuthController@login');
Router::post('/forgot-password', 'AuthController@forgotPassword');
Router::post('/reset-password', 'AuthController@resetPassword');
Router::get('/reservations/search/{search}', 'ReservationController@search', 'protected');

Router::dispatch();
