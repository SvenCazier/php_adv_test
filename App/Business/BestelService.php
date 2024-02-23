<?php
//App/Business/BestelService.php
declare(strict_types=1);

namespace App\Business;

use App\Business\{ErrorService, SessionService};
use App\Data\{BestelDAO, ProductDAO};
use App\Entities\{Winkelmandje, Pizza, Extra, Klant};
use App\Exceptions\{InvalidProductIdException, WinkelmandjeLeegException, GeenKlantGegevensException};
use Exception;

class BestelService
{
    private BestelDAO $bestelDAO;
    private ProductDAO $productDAO;

    public function __construct()
    {
        $this->bestelDAO = new BestelDAO();
        $this->productDAO = new ProductDAO();
    }

    public function voegToeAanWinkelMandje(int $pizzaId, array $extras, int $aantal): void
    {
        $winkelmandje = $this->getWinkelMandje();
        $extras[] = $pizzaId; // OM ALLE IDS TEZAMEN TE KUNNEN OPZOEKEN
        try {
            $result = $this->productDAO->getProductByIds($extras);
            if ($result) {
                // HAAL DE PIZZA (ER MAG ER MAAR 1 ZIJN) UIT HET RESULTAAT
                $type = 1;
                $pizzaResult = array_filter($result, function ($product) use ($type) {
                    return (int)$product["type"] === $type;
                });
                if (count($pizzaResult) === 1) {
                    // VERWIJDER DE PIZZA UIT HET RESULTAAT
                    $pizzaId = array_column($pizzaResult, "id");
                    $extraResult = array_filter($result, function ($product) use ($pizzaId) {
                        return !in_array($product["id"], $pizzaId);
                    });
                    $pizza = new Pizza((int)$pizzaResult[0]["id"], $pizzaResult[0]["naam"], $pizzaResult[0]["omschrijving"], (float)$pizzaResult[0]["prijs"], (float)$pizzaResult[0]["promotiePct"]);
                    foreach ($extraResult as $extra) {
                        $pizza->setExtra(new Extra((int)$extra["id"], $extra["naam"], $extra["omschrijving"], (float)$extra["prijs"], (float)$extra["promotiePct"]));
                    }
                    $winkelmandje->voegBestelLijnToe($pizza, $aantal);
                    SessionService::setSession("winkelmandje", $winkelmandje);
                } else {
                    //ALS ER NIET WELGETELD 1 PIZZA IN HET RESULSTAAT IS (ALS ER MEERDERE ZIJN IS EEN PIZZA ID MEEGEGEVEN ALS EXTRA ID)
                    throw new InvalidProductIdException();
                }
            } else {
                // ALS ER GEEN RESULTAAT IS
                throw new InvalidProductIdException();
            }
        } catch (Exception $e) {
            ErrorService::setError($e->getMessage());
        }
    }

    public function getWinkelMandje(): Winkelmandje
    {
        return SessionService::getSession("winkelmandje") ?? new Winkelmandje();
    }

    public function verwijderUitWinkelMandje(int $index): void
    {
        $winkelmandje = $this->getWinkelMandje();
        if ($winkelmandje) {
            $winkelmandje->verwijderBestelLijn($index);
            SessionService::setSession("winkelmandje", $winkelmandje);
        }
    }

    public function leegWinkelMandje(): void
    {
        SessionService::clearSessionVariable("winkelmandje");
    }

    public function afrekenen(): bool
    {
        $winkelmandje = $this->getWinkelMandje();
        try {
            if ($winkelmandje->isLeeg()) {
                throw new WinkelmandjeLeegException();
            } else if (!$winkelmandje->getKlant()) {
                throw new GeenKlantGegevensException();
            } else {
                $klant = $winkelmandje->getKlant();
                $this->bestelDAO->beginTransaction();
                $bestelId = $this->bestelDAO->maakBestelling($klant->getId());

                foreach ($winkelmandje->getbestelLijnen() as $bestelLijn) {
                    // VOOR IEDER PIZZA IN IN HET WINKELMANDJE MAAK EEN BESTELLIJN
                    $pizza = $bestelLijn["pizza"];
                    $bestelLijnId = $this->bestelDAO->maakBestelLijnPizza($bestelId, $pizza->getId(), $bestelLijn["aantal"], $pizza->getPrijsVoorKlant($klant));
                    $extras = $pizza->getExtras();

                    // VOOR IEDERE EXTRA OP EEN PIZZA MAAKT EEN BESTELLIJN
                    foreach ($extras as $extra) {
                        $this->bestelDAO->maakBestelLijnExtra($bestelLijnId, $extra->getId(), $extra->getPrijsVoorKlant($klant));
                    }
                }
                $this->bestelDAO->commitTransaction();
                $this->leegWinkelMandje();
                SessionService::setSession("klaarOmAfterekenen", 0);
                return true;
            }
        } catch (WinkelmandjeLeegException | GeenKlantGegevensException $e) {
            // APART CATCHEN OMDAT HIER GEEN ROLLBACK MOET GEBEUREN
            ErrorService::setError($e->getMessage());
            return false;
        } catch (Exception $e) {
            ErrorService::setError($e->getMessage());
            $this->bestelDAO->rollBackTransaction();
            return false;
        }
    }

    public function setKlaarOmAfTeRekenen(): void
    {
        // HEEFT OP KNOP GEDRUKT OM AF TE REKENEN
        SessionService::setSession("klaarOmAfterekenen", 1);
    }

    public function unsetKlaarOmAfTeRekenen(): void
    {
        SessionService::clearSessionVariable("klaarOmAfterekenen");
    }

    public function isKlaarOmAfTeRekeken(): bool
    {
        // ALS WINKELMANDJE LEEG => KAN NIET KLAAR ZIJN OM AF TE REKENEN
        if ($this->getWinkelMandje()->isLeeg()) {
            return false;
        } else {
            return (bool)SessionService::getSession("klaarOmAfterekenen") ?? false;
        }
    }

    public function setKlant(Klant $klant)
    {
        $winkelmandje = $this->getWinkelMandje();
        $winkelmandje->setKlant($klant);
        SessionService::setSession("winkelmandje", $winkelmandje); // MAAK OF UPDATE WINKELMANDJE SESSIE MET KLANT
    }

    public function getKlant(): ?Klant
    {
        $winkelmandje = SessionService::getSession("winkelmandje");
        if ($winkelmandje) return $winkelmandje->getKlant();
        return null;
    }

    public function unsetKlant()
    {
        $winkelmandje = $this->getWinkelMandje();
        $winkelmandje->unsetKlant();
        SessionService::setSession("winkelmandje", $winkelmandje); // MAAK OF UPDATE WINKELMANDJE SESSIE ZONDER KLANT
    }
}
