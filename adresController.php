<?php
//adresController.php
declare(strict_types=1);

require_once("bootstrap.php");

use App\Business\{BestelService, KlantService, ErrorService, InputService, RequestService, RedirectService};

/* TOON FORMULIER OM ADRES IN TE VULLEN */
/* ALS AANGEVINKT OM TE REGISTREREN => EN GA NAAR REGISTREETCONTROLLER */
/* ANDERS => GA NAAR OVERZICHTCONTROLLER */
/* ALS AUTHENTICATED => ADRESWIJZIGING */

$klantService = new KlantService();
$bestelService = new BestelService();

if (RequestService::isPost()) {
    $inputs = RequestService::postArray();
    // OPVANGEN ONTBREKEN VAN POST VARIABELEN VOOR SELECT & CHECKBOX INDIEN GEEN SELECTIE GEMAAKT
    if (!isset($inputs["woonplaats"])) $inputs["woonplaats"] = "";
    if (!isset($inputs["account_aanmaken"])) $inputs["account_aanmaken"] = "0";
    $inputs = InputService::validateInputs(
        $inputs,
        [
            "naam" => [InputService::TEXT, InputService::NOT_EMPTY],
            "voornaam" => [InputService::TEXT, InputService::NOT_EMPTY],
            "straat" => [InputService::TEXT, InputService::NOT_EMPTY],
            "huisnummer" => [InputService::HOUSE_NUMBER, InputService::NOT_EMPTY],
            "woonplaats" => [InputService::INTEGER, InputService::NOT_EMPTY],
            "telefoon" => [InputService::PHONE_NUMBER, InputService::NOT_EMPTY],
            "account_aanmaken" => [InputService::BOOLEAN]
        ]
    );
    if ($inputs) {
        $klant = null;
        if ($bestelService->getKlant()) { // ALS KLANTGEGEVENS IN WINKELMANDJE
            $klant = $klantService->wijzigKlantGegevens(
                $bestelService->getKlant(),
                $inputs["naam"],
                $inputs["voornaam"],
                $inputs["straat"],
                $inputs["huisnummer"],
                (int)$inputs["woonplaats"],
                $inputs["telefoon"]
            );
        } else {
            $klant = $klantService->bewaarKlantGegevens(
                $inputs["naam"],
                $inputs["voornaam"],
                $inputs["straat"],
                $inputs["huisnummer"],
                (int)$inputs["woonplaats"],
                $inputs["telefoon"]
            );
        }
        if ($klant) {
            $bestelService->setKlant($klant); // UPDATE KLANTGEGEVENS IN WINKELMANDJE
            if ($inputs["account_aanmaken"]) RedirectService::redirectTo("registreerController.php"); // ALS KLANT ZICH WIL REGISTREREN
            RedirectService::redirectTo("overzichtController.php");
        }
    }
}

print $twig->render(
    "adresFormulierTemplate.twig",
    array(
        "isAuthenticated" => $klantService->isAuthenticated(),
        "klant" => $bestelService->getKlant(),
        "post" => RequestService::postArray(),
        "plaatsen" => $klantService->getPlaatsenLijst(),
        "errors" => ErrorService::getErrors()
    )
);
