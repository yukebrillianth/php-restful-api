<?php

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private PDO $pdo;
    private PDOStatement | false $stmt;

    /**
     * Constructor
     * 
     * Establishes a database connection using PDO.
     */
    public function __construct()
    {
        $host = config('database.host');
        $port = config('database.port');
        $user = config('database.username');
        $database = config('database.database');
        $password = config('database.password');

        $connection = "pgsql:host=$host;port=$port;dbname=$database";

        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->pdo = new PDO($connection, $user, $password, $options);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Prepare a SQL query for execution
     *
     * @param string $sql
     * @return void
     */
    public function query(string $sql): void
    {
        $this->stmt = $this->pdo->prepare($sql);
    }

    /**
     * Bind parameters to the prepared statement.
     *
     * @param string $param Parameter placeholder in the SQL query
     * @param mixed $value Value to bind
     * @param int $type Data type of the parameter (optional)
     * @return void
     */
    public function bind(string $param, $value, int $type = null): void
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Execute the prepared statement.
     * 
     * @return bool True on success, false on failure.
     */
    public function execute(): bool
    {
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            Logger::error($e);
            jsonResponse([
                'code' => 500,
                'success' => false,
                'message' => "Internal Server Error"
            ]);
            die();
        }
    }

    /**
     * Fetch all rows from the result set.
     * 
     * @return array An array containing all of the remaining rows in the result set.
     */
    public function resultSet(): array
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a single row from the result set.
     * 
     * @return mixed An array representing the fetched row.
     */
    public function single(): mixed
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Start a transaction.
     */
    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction.
     */
    public function commit()
    {
        $this->pdo->commit();
    }

    /**
     * Roll back a transaction.
     */
    public function rollBack()
    {
        $this->pdo->rollBack();
    }

    /**
     * Get the ID of the last inserted row.
     * 
     * @return string The ID of the last inserted row.
     */
    public function lastInsertId(): string | false
    {
        return $this->pdo->lastInsertId();
    }
}
