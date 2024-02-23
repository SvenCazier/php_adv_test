<?php
//App/Data/DBConfig.php
namespace App\Data;

use PDO;
use PDOException;

abstract class DBConfig
{
    private string $dbConn = "mysql:host=localhost;dbname=eindproef_php_adv;charset=utf8";
    private string $dbUsername = "root";
    private string $dbPassword = "";
    private ?PDO $dbh;

    // NORMAL CONNECTION
    protected function connect(): void
    {
        try {
            $this->dbh = new PDO($this->dbConn, $this->dbUsername, $this->dbPassword);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    protected function connection(): PDO
    {
        return $this->dbh;
    }

    protected function disconnect(): void
    {
        $this->dbh = null;
    }

    // TRANSACTIONS
    public function beginTransaction(): void
    {
        $this->connect();
        $this->dbh->beginTransaction();
    }

    public function commitTransaction(): void
    {
        $this->dbh->commit();
        $this->disconnect();
    }

    public function rollBackTransaction(): void
    {
        $this->dbh->rollBack();
        $this->disconnect();
    }

    // CRUD OPERATIONS
    protected function create(string $sql, array $params): int
    {
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($params);
            return (int)$this->dbh->lastInsertId();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    protected function readAll(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    protected function readOne(string $sql, array $params): array|bool
    {
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    protected function update(string $sql, array $params): int
    {
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    protected function delete(string $sql, array $params): int
    {
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
