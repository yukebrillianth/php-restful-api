<?php

namespace App\Core;

use App\Core\Database;

class Model
{
    protected Database $db;
    protected string $table;
    protected array $hidden = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->table = $this->getDefaultTableName();
    }

    /**
     * Static method to get a new instance of the class.
     */
    public static function query(): static
    {
        return new static();
    }

    /**
     * Get query builder with columns selections
     *
     * @param string|null $columns
     * @return array|false
     */
    public function get(string $columns = null): array | false
    {
        if (!$columns) {
            // Filter kolom yang disembunyikan
            if ($this->hidden) {
                // Ambil semua kolom dari tabel
                $allColumns = $this->getAllColumns();
                $filteredColumns = array_filter($allColumns, function ($column) {
                    return !in_array($column, $this->hidden);
                });
                $columns = implode(', ', $filteredColumns);
            } else {
                $columns = '*';
            }
        } else {
            $selectedColumns = func_get_args();
            // Filter kolom yang disembunyikan
            if ($this->hidden) {
                $filteredColumns = array_filter($selectedColumns, function ($column) {
                    return !in_array($column, $this->hidden);
                });
                $columns = implode(', ', $filteredColumns);
            } else {
                $columns = implode(', ', $selectedColumns);
            }
        }

        $query = "SELECT $columns FROM $this->table";

        $this->db->query($query);
        $this->db->execute();

        return $this->db->resultSet();
    }

    /**
     * Get all columns of the table.
     *
     * @return array
     */
    protected function getAllColumns(): array
    {
        $query = "
            SELECT column_name 
            FROM information_schema.columns 
            WHERE table_name = :table_name
        ";
        $this->db->query($query);
        $this->db->bind(':table_name', $this->table);
        $this->db->execute();

        $columns = $this->db->resultSet();
        return array_map(function ($column) {
            return $column['column_name'];
        }, $columns);
    }

    /**
     * Get the default table name based on the child class name.
     */
    protected function getDefaultTableName(): string
    {
        // Get the name of the child class
        $className = static::class;

        // Remove namespace if present
        $className = substr($className, strrpos($className, '\\') + 1);

        // Pluralize the class name to get the table name
        $tableName = strtolower($this->pluralize($className));

        return $tableName;
    }

    /**
     * Pluralize a word.
     */
    protected function pluralize(string $word): string
    {
        // Implement your pluralization logic here
        // This is just a basic example
        return $word . 's'; // Simple pluralization by appending 's'
    }

    // Common functionality or methods for models can be added here
}
