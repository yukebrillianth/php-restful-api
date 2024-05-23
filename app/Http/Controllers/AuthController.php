<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Logger;
use App\Http\Services\Auth;
use App\Models\Book;
use App\Models\User;
use Firebase\JWT\JWT;
use PDOException;

class AuthController extends Controller
{
    public function signin()
    {
        // Validate input data
        $validationResult = $this->validate($_POST);
        if ($validationResult !== true) {
            return jsonResponse(['success' => false, 'message' => "Validation Error", 'errors' => $validationResult], 400);
        }

        try {
            // check is user exist
            $userExist = User::getInstance()->findByEmail($_POST['email']);

            if (!$userExist) {
                return jsonResponse(['success' => false, 'message' => "Invalid credentials"], 400);
            }

            // compare password
            $isPasswordValid = password_verify($_POST['password'], $userExist['password']);

            if (!$isPasswordValid) {
                return jsonResponse(['success' => false, 'message' => "Invalid credentials"], 400);
            }

            // generate JWT token
            $payload = [
                "user_id" => $userExist['id'],
                "exp" => time() + 3600 // Kadaluarsa dalam 1 jam
            ];

            // Buat token JWT
            $token = JWT::encode($payload, config('auth.JWT_SECRET'), 'HS256');

            // delete secret data
            unset($userExist['password']);
            unset($userExist['updated_at']);
            unset($userExist['created_at']);

            return jsonResponse(['success' => true, 'message' => 'Book added successfully.', 'data' => ['user' => $userExist, 'access_token' => $token]]);
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

    public function profile()
    {
        $user = Auth::user();
        unset($user['password']);
        unset($user['updated_at']);
        unset($user['created_at']);
        return jsonResponse(['success' => true, 'message' => 'Get user profile successfully.', 'data' => ['user' => $user]]);
    }

    private function validate(array $data)
    {
        $errors = [];

        // Validate email
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required.';
        }
        // else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        //     $errors['email'] = 'Not a valid email.';
        // }

        // Validate password
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required.';
        }
        // else if ($data['password'] < 8) {
        //     $errors['password'] = 'Password must be more than 8 characters.';
        // }

        return empty($errors) ? true : $errors;
    }
}
