<?php

namespace App\Core;

use App\Core\Database;

class Model
{
    protected Database $db;
    protected string $table;

    /**
     * 
     * @var Singleton
     */
    private static $instance;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->table = $this->getDefaultTableName();
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
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
