<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\HomeController;
use App\Http\Middlewares\Auth;
use App\Http\Middlewares\JsonParser;

Router::group(['prefix' => '/v1/books', 'middleware' => [JsonParser::class, Auth::class]], function () {
    Router::add('GET', '/', BookController::class, 'getByUser');
    Router::add('GET', '/all', BookController::class, 'get');
    Router::add('GET', '/with', BookController::class, 'getWithAuthors');
    Router::add('GET', '/search/<keyword>', BookController::class, 'search');
    Router::add('GET', '/<uuid>', BookController::class, 'show');
    Router::add('GET', '/<uuid>/author/<id>', BookController::class, 'show2');
    Router::add('POST', '/', BookController::class, 'create');
});

Router::group(['prefix' => '/v1/auth', 'middleware' => [JsonParser::class]], function () {
    Router::add('POST', '/signin', AuthController::class, 'signin');
    Router::add('GET', '/profile', AuthController::class, 'profile', [Auth::class]);
});

Router::add('GET', '/v1/test', HomeController::class, 'test');
