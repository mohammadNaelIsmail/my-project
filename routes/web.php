<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});
Route::get('/events', function () {
    return view('events');
});
Route::get('/humans', function () {
    return view('humans');
});
