<?php
//App/Data/PlaatsDAO.php
namespace App\Data;

use PDO;
use PDOException;

class PlaatsDAO extends DBConfig
{
    public function getPlaatsById(int $plaatsId): array|bool
    {
        $sql = "SELECT
					id,
					postcode,
                    woonplaats
                FROM 
                    plaatsen
                WHERE 
                    id = :plaatsId";
        try {
            $this->connect();
            $result = $this->readOne($sql, ["plaatsId" => $plaatsId]);
            $this->disconnect();
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getAllPlaatsen(): array
    {
        $sql = "SELECT
					id,
					postcode,
                    woonplaats
                FROM 
                    plaatsen
                ORDER BY 
                    postcode ASC";
        try {
            $this->connect();
            $result = $this->readAll($sql);
            $this->disconnect();
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
