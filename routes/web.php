<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-connection', function () {
    $response = Http::withHeaders([
        'X-Parse-Application-Id' => env('BACK4APP_APP_ID'),
        'X-Parse-REST-API-Key' => env('BACK4APP_REST_KEY'),
        'Content-Type' => 'application/json',
    ])->post('https://parseapi.back4app.com/classes/Playground', [
        'name' => 'ملعب الاختبار التجريبي'
    ]);

    return $response->json();
});
