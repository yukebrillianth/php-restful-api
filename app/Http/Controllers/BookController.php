<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Logger;
use App\Http\Services\Auth;
use App\Models\Book;
use PDOException;

class BookController extends Controller
{
    public function get()
    {
        try {
            $books = Book::query()->findAll();

            if ($books) {
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $books
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Books not found'
                ], 404);
            }
        } catch (\Throwable $e) {
            Logger::error($e);
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function show($uuid)
    {
        try {
            $book = Book::query()->findByUuid($uuid);

            if ($book) {
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $book
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Book not found'
                ], 404);
            }
        } catch (\Throwable $e) {
            Logger::error($e);
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function search($keyword)
    {
        try {
            $books = Book::query()->search($keyword);

            if ($books) {
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $books
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Books not found'
                ], 404);
            }
        } catch (\Throwable $e) {
            Logger::error($e);
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function create()
    {
        // Validate input data
        $validationResult = $this->validate($_POST);
        if ($validationResult !== true) {
            return $this->jsonResponse([
                'success' => false,
                'message' => "Validation Error",
                'errors' => $validationResult
            ], 400);
        }

        $data = [
            'title' => $_POST['title'],
            'author_id' => Auth::user()['id'],
            'description' => $_POST['description'],
        ];

        try {
            $insertedId = Book::query()->create($data);
            if ($insertedId) {
                Logger::log("New book created with ID " . $insertedId);
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Book added successfully.',
                    'data' => [
                        'id' => $insertedId
                    ]
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Failed to add book.'
                ], 500);
            }
        } catch (\Throwable $e) {
            Logger::error($e);
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function getWithAuthors()
    {
        try {
            $books = Book::query()->withAuthors();

            if ($books) {
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $books
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Books not found'
                ], 404);
            }
        } catch (\Throwable $e) {
            Logger::error($e);
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function getByUser()
    {
        try {
            $books = Book::query()->byUser(Auth::user()['id']);

            if ($books) {
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $books
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Books not found'
                ], 404);
            }
        } catch (\Throwable $e) {
            Logger::error($e);
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function show2($uuid, $id)
    {
        return $this->jsonResponse(['uuid' => $uuid, 'id' => $id]);
    }

    private function validate(array $data)
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Title is required.';
        }
        if (!isset($data['description'])) {
            $errors['description'] = 'Description is required.';
        }

        return empty($errors) ? true : $errors;
    }
}
