<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Models\Book;

class HomeController extends Controller
{
    public function index()
    {
        return $this->jsonResponse(['status' => 'OK GES']);
    }

    public function test()
    {
        return jsonResponse(Book::query()->get());
    }
}
