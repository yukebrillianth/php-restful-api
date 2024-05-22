<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Logger;
use App\Models\Book;
use PDOException;

class BookController extends Controller
{
    public function get()
    {
        try {
            try {
                $book = Book::getInstance()->findAll();
            } catch (PDOException  $e) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Internal Server Error'
                ], 500);
            }

            if ($book) {
                // Book found, return JSON response
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $book
                ]);
            } else {
                // Book not found, return JSON response with error
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Book not found'
                ], 404); // HTTP status code 404 for "Not Found"
            }
        } catch (\Throwable $e) {
            // Log the exception (you can use Logger class here)
            Logger::error($e);
            // Respond with internal server error
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function show($uuid)
    {
        try {
            try {
                $book = Book::getInstance()->findByUuid($uuid);
            } catch (PDOException  $e) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Internal Server Error'
                ], 500);
            }

            if ($book) {
                // Book found, return JSON response
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $book
                ]);
            } else {
                // Book not found, return JSON response with error
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Book not found'
                ], 404); // HTTP status code 404 for "Not Found"
            }
        } catch (\Throwable $e) {
            // Log the exception (you can use Logger class here)
            Logger::error($e);
            // Respond with internal server error
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function search($keyword)
    {
        try {
            $book = Book::getInstance()->search($keyword);

            if ($book) {
                // Book found, return JSON response
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $book
                ]);
            } else {
                // Book not found, return JSON response with error
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Book not found'
                ], 404); // HTTP status code 404 for "Not Found"
            }
        } catch (\Throwable $e) {
            // Log the exception (you can use Logger class here)
            Logger::error($e);
            // Respond with internal server error
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
            return jsonResponse(['success' => false, 'message' => "Validation Error", 'errors' => $validationResult], 400);
        }

        $data = [
            'title' => $_POST['title'],
            'author' => $_POST['author'],
            'description' => $_POST['description'],
        ];

        try {
            $insertedId = Book::getInstance()->create($data);
            if ($insertedId) {
                Logger::log("New book created with " . logData($insertedId));
                return jsonResponse(['success' => true, 'message' => 'Book added successfully.', 'id' => $insertedId]);
            } else {
                return jsonResponse(['success' => false, 'message' => 'Failed to add book.'], 500);
            }
        } catch (\Throwable $e) {
            // Log the exception (you can use Logger class here)
            Logger::error($e);
            // Respond with internal server error
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
        if (empty($data['author'])) {
            $errors['author'] = 'Author is required.';
        }
        if (!isset($data['description'])) {
            $errors['description'] = 'Description is required.';
        }

        return empty($errors) ? true : $errors;
    }
}
