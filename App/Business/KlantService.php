<?php
//App/Business/KlantService.php
declare(strict_types=1);

namespace App\Business;

use App\Business\{ErrorService, CookieService, SessionService};
use App\Data\{KlantDAO, PlaatsDAO};
use App\Entities\{Klant, Adres, Plaats};
use App\Exceptions\{InvalidUserCredentialsException, InvalidLocationException, EmailExistsException};
use Exception;
use PDOException;

class KlantService
{
    private KlantDAO $KlantDAO;
    private PlaatsDAO $PlaatsDAO;

    public function __construct()
    {
        $this->KlantDAO = new KlantDAO();
        $this->PlaatsDAO = new PlaatsDAO();
    }
    public function login(string $email, string $wachtwoord): ?Klant
    {
        try {
            $result = $this->KlantDAO->getKlantWachtwoord($email);
            if (!$result || !password_verify($wachtwoord, $result["wachtwoord"])) throw new InvalidUserCredentialsException();

            $result = $this->KlantDAO->getKlantGegevensById($result["id"]);
            $klant = new Klant(
                (int) $result["id"],
                $result["naam"],
                $result["voornaam"],
                new Adres(
                    $result["straat"],
                    $result["huisnummer"],
                    new Plaats(
                        (int) $result["plaatsId"],
                        (int) $result["postcode"],
                        $result["woonplaats"]
                    ),
                ),
                $result["telefoon"],
                (bool) $result["rechtOpPromotie"],
                $result["email"]
            );
            $this->setSessionAndCookie($klant);
            return $klant;
        } catch (Exception $e) {
            ErrorService::setError($e->getMessage());
            return null;
        }
    }

    public function bewaarKlantGegevens(string $naam, string $voornaam, string $straat, string $huisnummer, int $plaatsId, string $telefoon): ?Klant
    {
        // VRAAG POSTCODE IDS OP ALS ID NIET IN LIJST DAN POSTCODE NIET TOEGESTAAN
        try {
            $plaatsResult = $this->PlaatsDAO->getPlaatsById($plaatsId);
            if (!$plaatsResult) throw new InvalidLocationException();

            // KLANT MOET ALTIJD AANGEMAAKT WORDEN, OOK ZONDER REGISTRATIE
            $klantId = $this->KlantDAO->maakKlant($naam, $voornaam, $straat, $huisnummer, $plaatsId, $telefoon);

            return new Klant(
                (int) $klantId,
                $naam,
                $voornaam,
                new Adres(
                    $straat,
                    $huisnummer,
                    new Plaats(
                        (int) $plaatsId,
                        (int) $plaatsResult["postcode"],
                        $plaatsResult["woonplaats"]
                    ),
                ),
                $telefoon,
                false // NIEUWE OF NIET GEREGISTREERDE/INGELOGDE KLANT HEEFT GEEN RECHT OP PROMOTIE
            );
        } catch (Exception $e) {
            ErrorService::setError($e->getMessage());
            return null;
        }
    }

    public function wijzigKlantGegevens(Klant $klant, string $naam, string $voornaam, string $straat, string $huisnummer, int $plaatsId, string $telefoon): ?Klant
    {
        if (!$klant) return $klant; // REDUNDANT CHECK VOOR DE ZEKERHEID
        // VRAAG POSTCODE IDS OP ALS ID NIET IN LIJST DAN POSTCODE NIET TOEGESTAAN
        try {
            $plaatsResult = $this->PlaatsDAO->getPlaatsById($plaatsId);
            if (!$plaatsResult) throw new InvalidLocationException();

            $rowCount = $this->KlantDAO->updateKlant($klant->getId(), $naam, $voornaam, $straat, $huisnummer, $plaatsId, $telefoon);
            // NIET BESTAAND ID KAN ENKEL ALS IEMAND MET DE SESSIE GEKNOEID HEEFT => GEEN FOUTMELDINGEN WEERGEVEN
            // NIET GEUPDATED ID BETEKENT ZELFDE GEGEVENS DOORGESTUURD => GEEN FOUTMELDINGEN WEERGEVEN
            if (!$rowCount) return $klant; // ALS ER NIETS GEUPDATED IS STUUR OUDE KLANTGEGEVENS TERUG

            return new Klant(
                (int) $klant->getId(),
                $naam,
                $voornaam,
                new Adres(
                    $straat,
                    $huisnummer,
                    new Plaats(
                        (int) $plaatsId,
                        (int) $plaatsResult["postcode"],
                        $plaatsResult["woonplaats"]
                    ),
                ),
                $telefoon,
                (bool) $klant->heeftRechtOpPromotie(), // HERGEBRUIK WANT WORDT NIET GEUPDATED
                $klant->getEmail() // HERGEBRUIK WANT WORDT NIET GEUPDATED
            );
        } catch (Exception $e) {
            ErrorService::setError($e->getMessage());
            return null;
        }
    }

    public function registreer(string $email, string $wachtwoord, ?Klant $klant): ?Klant
    {
        if (!$klant) return $klant; // REDUNDANT CHECK VOOR DE ZEKERHEID
        try {
            $result = $this->KlantDAO->emailExists($email);
            // CHECK OF EMAIL BESTAAT
            if ($result) throw new EmailExistsException();

            $this->KlantDAO->setKlantLogin(
                $klant->getId(),
                $email,
                password_hash($wachtwoord, PASSWORD_DEFAULT)
            );
            $klant->setEmail($email);
            $this->setSessionAndCookie($klant);
            return $klant;
        } catch (Exception $e) {
            ErrorService::setError($e->getMessage());
            return null;
        }
    }

    public function startKlantSession(?Klant $klant): void
    {
        if ($klant) {
            SessionService::setSession("klant", $klant);
        }
    }

    public function logout(): void
    {
        // VERWIJDER ZOWEL ENKEL DE SESSIE DIE BIJHOUDT OF DE KLANT IS AANGEMELD, NIET HET WINKELMANDJE
        SessionService::clearSessionVariable("klant");
    }

    public function isAuthenticated(): bool
    {
        if (SessionService::getSession("klant")) return true;
        return false;
    }

    public function getKlant(): ?Klant
    {
        return SessionService::getSession("klant");
    }

    public function getEmail(): ?string
    {
        return CookieService::getCookie("klantEmail");
    }

    public function getPlaatsenLijst(): array
    {
        try {
            $result = $this->PlaatsDAO->getAllPlaatsen();
            $plaatsenArray = [];
            foreach ($result as $plaats) {
                $plaatsenArray[] = new Plaats((int)$plaats["id"], (int)$plaats["postcode"], $plaats["woonplaats"]);
            }
            return $plaatsenArray;
        } catch (Exception $e) {
            ErrorService::setError($e->getMessage());
            return [];
        }
    }

    private function setSessionAndCookie(?Klant $klant)
    {
        $this->startKlantSession($klant);
        CookieService::setCookie(
            "klantEmail",
            $klant->getEmail(),
            time() + 60 * 60 * 24 * 30
        );
    }
}
