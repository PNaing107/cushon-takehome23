<?php

declare(strict_types=1);

namespace App\DataAccess;

use Dotenv\Dotenv;

class Database
{
    private \PDO $pdo;

    private static ?Database $instance = null;

    private function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $db = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USERNAME'];
        $pass = $_ENV['DB_PASSWORD'];

        $dsn = "mysql:host=$host;dbname=$db";

        $options = [
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $exception) {
            throw new \PDOException($exception->getMessage(), (int)$exception->getCode());
        }
    }

    /**
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->pdo;
    }

    /**
     * @param string $sql
     * @param array $values
     * @param string $fetchMode
     * @return \PDOStatement
     */
    private function prepareAndExecute(string $sql, array $values = [], array $fetchMode = [\PDO::FETCH_ASSOC]): \PDOStatement
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($values);
        $statement->setFetchMode(...$fetchMode);

        return $statement;
    }

    /**
     * @param string $sql
     * @param array $values
     * @return array
     */
    public function fetch(string $sql, array $values = [], array $fetchMode = [\PDO::FETCH_ASSOC]): mixed
    {
        $statement = $this->prepareAndExecute($sql, $values, $fetchMode);

        return $statement->fetch();
    }

    /**
     * @param string $sql
     * @param array $values
     * @return array
     */
    public function fetchAll(string $sql, array $values = [], array $fetchMode = [\PDO::FETCH_ASSOC]): array
    {
        $statement = $this->prepareAndExecute($sql, $values, $fetchMode);

        return $statement->fetchAll();
    }
}
