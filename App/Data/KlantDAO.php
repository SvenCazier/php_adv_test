<?php
//App/Data/KlantDAO.php
namespace App\Data;

use PDO;
use PDOException;

class KlantDAO extends DBConfig
{
    public function maakKlant(
        string $naam,
        string $voornaam,
        string $straat,
        string $huisnummer,
        int $plaatsId,
        string $telefoon
    ): int {
        $sql = "INSERT INTO
                    klanten (
                        naam,
                        voornaam, 
                        straat,
                        huisnummer,
                        plaatsId,
                        telefoon
                    ) 
                VALUES 
                    (
                        :naam,
                        :voornaam,
                        :straat,
                        :huisnummer,
                        :plaatsId,
                        :telefoon
                    )
                ON DUPLICATE KEY
                UPDATE id = LAST_INSERT_ID(id)";
        try {
            $this->connect();
            $result = $this->create(
                $sql,
                [
                    "naam" => $naam,
                    "voornaam" => $voornaam,
                    "straat" => $straat,
                    "huisnummer" => $huisnummer,
                    "plaatsId" => $plaatsId,
                    "telefoon" => $telefoon,
                ]
            );
            $this->disconnect();
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function updateKlant(
        int $klantId,
        string $naam,
        string $voornaam,
        string $straat,
        string $huisnummer,
        int $plaatsId,
        string $telefoon
    ): int {
        $sql = "UPDATE 
                    klanten 
                SET 
                    naam = :naam, 
                    voornaam = :voornaam, 
                    straat = :straat, 
                    huisnummer = :huisnummer, 
                    plaatsId = :plaatsId, 
                    telefoon = :telefoon 
                WHERE 
                    id = :klantId";
        try {
            $this->connect();
            $result = $this->update(
                $sql,
                [
                    "klantId" => $klantId,
                    "naam" => $naam,
                    "voornaam" => $voornaam,
                    "straat" => $straat,
                    "huisnummer" => $huisnummer,
                    "plaatsId" => $plaatsId,
                    "telefoon" => $telefoon
                ]
            );
            $this->disconnect();
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function setKlantLogin(int $klantId, string $email, string $wachtwoord): int
    {
        $sql = "UPDATE klanten
                SET email = :email, wachtwoord = :wachtwoord
                WHERE id = :id";
        try {
            $this->connect();
            $result = $this->update(
                $sql,
                [
                    "id" => $klantId,
                    "email" => $email,
                    "wachtwoord" => $wachtwoord
                ]
            );
            $this->disconnect();
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getKlantGegevensById(int $klantId): array
    {
        $sql = "SELECT
                    klanten.id,
                    naam,
                    voornaam,
                    straat,
                    huisnummer,
                    plaatsId,
                    postcode,
                    woonplaats,
                    telefoon,
                    email,
                    rechtOpPromotie
                FROM klanten
                JOIN plaatsen ON klanten.plaatsId = plaatsen.id
                WHERE klanten.id = :klantId";
        try {
            $this->connect();
            $result = $this->readOne($sql, ["klantId" => $klantId]);
            $this->disconnect();
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getKlantEmailById(int $id): array|bool
    {
        $sql = "SELECT id, email
				FROM klanten
				WHERE id = :id";
        try {
            $this->connect();
            $result = $this->readOne($sql, ["id" => $id]);
            $this->disconnect();
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function emailExists(string $email): array|bool
    {
        $sql = "SELECT id
				FROM klanten
				WHERE email = :email";
        try {
            $this->connect();
            $result = $this->readOne($sql, ["email" => $email]);
            $this->disconnect();
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getKlantWachtwoord(string $email): array|bool
    {
        $sql = "SELECT id, wachtwoord
				FROM klanten
				WHERE email = :email";
        try {
            $this->connect();
            $result = $this->readOne($sql, ["email" => $email]);
            $this->disconnect();
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
