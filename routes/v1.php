<?php

use App\Http\Controllers\BookController;
use App\Http\Middlewares\JsonParser;

Router::group(['prefix' => '/v1/books', 'middleware' => [JsonParser::class]], function () {
    Router::add('GET', '/', BookController::class, 'get');
    Router::add('GET', '/search/<keyword>', BookController::class, 'search');
    Router::add('GET', '/<uuid>', BookController::class, 'show');
    Router::add('GET', '/<uuid>/author/<id>', BookController::class, 'show2');
    Router::add('POST', '/', BookController::class, 'create');
});
