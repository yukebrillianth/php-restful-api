<?php

namespace App\Models;

use App\Core\Logger;
use App\Core\Model;
use PDOException;

class User extends Model
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

    public function findByEmail(string $email): array | false
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";

        $this->db->query($query);
        $this->db->bind(':email', $email);
        $this->db->execute();

        return $this->db->single();
    }

    public function create(array $data): array | false
    {
        try {
            $this->db->beginTransaction();
            $query = "INSERT INTO $this->table (full_name, email, password) VALUES (:full_name, :email, :password) RETURNING full_name, email";

            $this->db->query($query);
            $this->db->bind(':full_name', $data['full_name']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':password', $data['password']);

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
