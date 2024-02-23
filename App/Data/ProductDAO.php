<?php
//App/Data/ProductDAO.php
namespace App\Data;

use PDO;
use PDOException;

class ProductDAO extends DBConfig
{
    public function getAllProductenByType(int $type): array
    {
        $sql = "SELECT 
                    id, 
                    naam, 
                    omschrijving, 
                    prijs, 
                    promotiePct 
                FROM 
                    producten 
                WHERE 
                    type = :type";
        try {
            $this->connect();
            $result = $this->readAll($sql, ["type" => $type]);
            $this->disconnect();
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getProductById(int $id): array
    {
        $sql = "SELECT 
                    id, 
                    naam, 
                    omschrijving, 
                    prijs, 
                    promotiePct 
                FROM 
                    producten 
                WHERE 
                    id = :id";
        try {
            $this->connect();
            $result = $this->readOne($sql, ["id" => $id]);
            $this->disconnect();
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getProductByIds(array $ids): array
    {
        $placeholders = rtrim(str_repeat('?,', count($ids)), ',');
        $sql = "SELECT 
                    id,
                    type,
                    naam, 
                    omschrijving, 
                    prijs, 
                    promotiePct 
                FROM 
                    producten 
                WHERE 
                    id IN ($placeholders)";
        try {
            $this->connect();
            $result = $this->readAll($sql, $ids);
            $this->disconnect();
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
