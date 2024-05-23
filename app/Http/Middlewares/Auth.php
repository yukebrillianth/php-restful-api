<?php

namespace App\Http\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
    public function before()
    {
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!$authorizationHeader) {
            exit(jsonResponse(['success' => false, "message" => "Unauthorized"], 401));
        } else {
            try {
                $this->verifyToken(str_replace('Bearer ', '', $authorizationHeader));
            } catch (\Throwable $e) {
                exit(jsonResponse(['success' => false, "message" => $e->getMessage()], 401));
            }
        }
    }

    private function verifyToken($token)
    {
        $decoded = JWT::decode($token, new Key(config('auth.JWT_SECRET'), 'HS256'));
        return $decoded->user_id;
    }
}
