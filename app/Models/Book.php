<?php

namespace App\Models;

use App\Core\Logger;
use App\Core\Model;
use PDOException;

class Book extends Model
{
    public function findAll(): array | false
    {
        $query = "SELECT * FROM " . $this->table;

        $this->db->query($query);
        $this->db->execute();

        return $this->db->resultSet();
    }

    public function findByUuid(string $uuid): array | false
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :uuid";

        try {
            $this->db->query($query);
            $this->db->bind(':uuid', $uuid);
            $this->db->execute();

            return $this->db->single();
        } catch (PDOException $e) {
            if ($e->getCode() === '22P02') {
                return false;
            } else {
                Logger::error($e);
                jsonResponse([
                    'code' => 500,
                    'success' => false,
                    'message' => "Internal Server Error"
                ], 500);
                die();
            }
        }
    }

    public function search(string $keyword): array | false
    {
        $query = "SELECT * FROM " . $this->table . " WHERE title ILIKE :keyword";

        $this->db->query($query);
        $this->db->bind(':keyword', "%$keyword%");
        $this->db->execute();

        return $this->db->resultSet();
    }

    public function create(array $data): string | false
    {
        try {
            $this->db->beginTransaction();

            // Query dengan RETURNING id
            $query = "INSERT INTO $this->table (title, author_id, description) VALUES (:title, :author_id, :description) RETURNING id";

            // Mempersiapkan dan mengikat parameter
            $this->db->query($query);
            $this->db->bind(':title', $data['title']);
            $this->db->bind(':author_id', $data['author_id']);
            $this->db->bind(':description', $data['description']);

            // Eksekusi dan ambil hasil
            if ($result = $this->db->single()) {
                $this->db->commit();

                return $result['id'];
            } else {
                Logger::log('Query execution failed');
                $this->db->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            // Rollback transaction on exception
            $this->db->rollBack();
            // Log the error
            Logger::error($e);
            // Return false or handle the exception accordingly
            return false;
        }
    }

    public function withAuthors(): array|false
    {
        $query = "SELECT books.*, users.full_name AS author_name, users.email AS author_email
        FROM books
        JOIN users ON books.author_id = users.id
        ORDER BY books.created_at ASC";

        $this->db->query($query);
        $this->db->execute();

        $books = $this->db->resultSet();
        $result = [];

        foreach ($books as $book) {
            $result[] = [
                'id' => $book['id'],
                'title' => $book['title'],
                'description' => $book['description'],
                'created_at' => $book['created_at'],
                'updated_at' => $book['updated_at'],
                'author' => [
                    'name' => $book['author_name'],
                    'email' => $book['author_email'],
                ],
            ];
        }
        return $result;
    }

    public function byUser(string $authorId): array|false
    {
        $query = "SELECT books.*, users.full_name AS author_name, users.email AS author_email
        FROM books
        JOIN users ON books.author_id = users.id
        WHERE books.author_id = :authorId
        ORDER BY books.created_at DESC";

        $this->db->query($query);
        $this->db->bind(':authorId', $authorId);
        $this->db->execute();

        $books = $this->db->resultSet();
        $result = [];

        foreach ($books as $book) {
            $result[] = [
                'id' => $book['id'],
                'title' => $book['title'],
                'description' => $book['description'],
                'created_at' => $book['created_at'],
                'updated_at' => $book['updated_at'],
                'author' => [
                    'name' => $book['author_name'],
                    'email' => $book['author_email'],
                ],
            ];
        }
        return $result;
    }
}
