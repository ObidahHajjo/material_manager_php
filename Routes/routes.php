<?php

use Config\Router;

Router::get('/', 'HomeController@index');

//Connexion
Router::get('/login', 'AuthController@display');
Router::get('/logout', 'AuthController@logOut', 'protected');
Router::get('/reset/password', 'AuthController@forgetPasswordFrom');
Router::get('/reset-password/{token}', 'AuthController@showResetForm');








// POST && API 
Router::post('/api/captcha', 'CaptchaController@validate');
Router::post('/login', 'AuthController@login');
Router::post('/forgot-password', 'AuthController@forgotPassword');
Router::post('/reset-password', 'AuthController@resetPassword');

Router::dispatch();
