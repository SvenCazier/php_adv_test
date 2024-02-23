<?php
//App/Data/BestelDAO.php
namespace App\Data;

use App\Entities\Bestelling;

use PDO;
use PDOException;

class BestelDAO extends DBConfig
{
    public function maakBestelling(int $klantId, string $koerierInfo = ""): int
    {
        // DATUM-TIJD WORDT AUTOMATISCH IN DB TOEGEVOEGD MET CURRENT_TIMESTAMP
        // GEEFT BESTEL ID TERUG OM BESTELLIJNEN TE MAKEN
        $sql = "INSERT INTO 
                    bestellingen (klantId, koerierInfo) 
                VALUES 
                    (:klantId, :koerierInfo)";
        try {
            return $this->create(
                $sql,
                [
                    "klantId" => $klantId,
                    "koerierInfo" => $koerierInfo
                ]
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function maakBestelLijnPizza(int $bestelId, int $pizzaId, int $aantal, float $bestelPrijs): int
    {
        // GEEFT BESTELLIJN ID TERUG OM EVENTUELE BESTELLIJNEN VOOR EXTRAS TE MAKEN
        $sql = "INSERT INTO
                    bestellijnen (bestelId, pizzaId, aantal, bestelPrijs) 
                VALUES 
                    (:bestelId, :pizzaId, :aantal, :bestelPrijs)";
        try {
            return $this->create(
                $sql,
                [
                    "bestelId" => $bestelId,
                    "pizzaId" => $pizzaId,
                    "aantal" => $aantal,
                    "bestelPrijs" => $bestelPrijs
                ]
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function maakBestelLijnExtra(int $bestelLijnId, int $extraId, float $bestelPrijs): int
    {
        $sql = "INSERT INTO
                    bestellijnen_extra (bestelLijnId, extraId, bestelPrijs) 
                VALUES 
                    (:bestelLijnId, :extraId, :bestelPrijs)";
        try {
            return $this->create(
                $sql,
                [
                    "bestelLijnId" => $bestelLijnId,
                    "extraId" => $extraId,
                    "bestelPrijs" => $bestelPrijs
                ]
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
