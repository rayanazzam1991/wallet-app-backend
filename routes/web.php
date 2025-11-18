<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pusher-test', function () {
    return view('pusher-test');
})->middleware('auth');
