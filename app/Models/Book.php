<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Logger;
use App\Core\Model;
use App\Models\Dtos\BookDTO;
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

        $this->db->query($query);
        $this->db->bind(':uuid', $uuid);
        $this->db->execute();

        return $this->db->single();
    }

    public function search(string $keyword): array | false
    {
        $query = "SELECT * FROM " . $this->table . " WHERE title ILIKE :keyword";

        $this->db->query($query);
        $this->db->bind(':keyword', "%$keyword%");
        $this->db->execute();

        return $this->db->resultSet();
    }

    public function create(array $data): array | false
    {
        try {
            $this->db->beginTransaction();
            $query = "INSERT INTO $this->table (title, author, description) VALUES (:title, :author, :description) RETURNING *";

            $this->db->query($query);
            $this->db->bind(':title', $data['title']);
            $this->db->bind(':author', $data['author']);
            $this->db->bind(':description', $data['description']);

            if ($this->db->execute()) {
                $this->db->commit();
                return $this->db->single();
            } else {
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
}
