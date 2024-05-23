<?php

namespace App\Http\Services;

use App\Core\Logger;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDOException;

class Auth
{
    private static ?array $user = null;

    public static function user(): ?array
    {
        // Jika data pengguna sudah diambil sebelumnya, kembalikan
        if (self::$user !== null) {
            return self::$user;
        }

        // Ambil token dari header Authorization
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        // Validasi dan ambil data dari token JWT
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7); // Hapus "Bearer " dari header

            // Lakukan validasi token JWT dan ambil data pengguna
            $decodedToken = JWT::decode($token, new Key(config('auth.JWT_SECRET'), 'HS256'));

            // Find user by uuid
            try {
                $user = User::query()->findByUuid($decodedToken->user_id);
                self::$user = $user ?? null;
                return self::$user;
            } catch (PDOException $e) {
                Logger::error($e);
                return null;
            }

            // Simpan data pengguna ke dalam properti statis untuk penggunaan selanjutnya

        }

        return null; // Jika tidak ada token atau token tidak valid
    }
}
