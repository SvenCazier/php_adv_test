<?php
//winkelmandjeController.php
declare(strict_types=1);

require_once("bootstrap.php");

use App\Business\{
    BestelService,
    KlantService,
    ErrorService,
    InputService,
    RedirectService,
    RequestService,
    SessionService
};

$klantService = new KlantService();
$bestelService = new BestelService();

if (RequestService::isPost()) { // HIER AFHANDELEN OM FORM RESUBMIT TE VOORKOMEN
    ErrorService::clearErrors();
    $inputs = RequestService::postArray();
    // OPVANGEN ONTBREKEN VAN POST VARIABELEN VOOR RADIO & CHECKBOX INDIEN GEEN SELECTIE GEMAAKT
    if (!isset($inputs["pizza"])) $inputs["pizza"] = NULL;
    if (!isset($inputs["extras"])) $inputs["extras"] = [];
    $inputs = InputService::validateInputs(
        $inputs,
        [ // BIJ INDEXNUMMERS ZOU OOK GETEST KUNNEN WORDEN OP NOT_LESS_THAN_OR_EQUAL_TO_ZERO, MAAR ALS ZE NIET BESTAAN VOLGT ER TOCH EEN ERROR IN DE SERVICE
            "pizza" => [InputService::INTEGER, InputService::NOT_EMPTY],
            "extras" => [InputService::INTEGER, InputService::IS_ARRAY], // OPTIONEEL, MAAR ARRAY
            "aantal" => [InputService::INTEGER, InputService::NOT_EMPTY, InputService::NOT_LESS_THAN_OR_EQUAL_TO_ZERO]
        ]
    );
    if ($inputs) {
        $bestelService->voegToeAanWinkelMandje(
            (int) $inputs["pizza"],
            InputService::convertStringArrayToIntArray($inputs["extras"]),
            (int) $inputs["aantal"]
        );
    }
    SessionService::setSession("fromWinkelmandjeController", true); // OM DUIDELIJK TE MAKEN AAN DE BESTELCONTROLLER DAT ER HANDELINGEN ZIJN GEBEURD, IVM ERRORS
}

if (RequestService::isGet()) {
    switch (RequestService::getKey("action")) {
        case "reset": // LEEG WINKELMANDJE
            $bestelService->leegWinkelMandje();
            SessionService::setSession("fromWinkelmandjeController", true); // OM DUIDELIJK TE MAKEN AAN DE BESTELCONTROLLER DAT ER HANDELINGEN ZIJN GEBEURD, IVM ERRORS
            break;
        case "order": // ALS KLAAR OM AF TE REKENEN
            if ($klantService->isAuthenticated()) $bestelService->setKlant($klantService->getKlant());
            $bestelService->setKlaarOmAfTeRekenen(); // OM LATER TE WETEN TE KOMEN OF KLANT BESTELLING WIL AFRONDEN OF NIET IN LOGINCONTROLLER
            // ALS AANGEMELDE GEBRUIKER OF GASTGEBRUIKER DIE ADRES REEDS HEEFT INGEVULD MAAR BESTELLING HEEFT GEWIJZIGD => GA NAAR OVERZICHTCONTROLLER
            if ($bestelService->getKlant()) RedirectService::redirectTo("overzichtController.php");
            RedirectService::redirectTo("keuzeController.php");
            break;
        case "delete": // VERWIJDER PIZZA UIT WINKELMANDJE
            $query = RequestService::getArray();
            unset($query['action']); // VERWIJDER DE "ACTION" QUERY PARAMETER WANT DIT IS GEEN USER INPUT DIE GEVALIDEERD DIENT TE WORDEN
            $query = InputService::validateInputs( // NIET NOODZAKELIJK OM ERRORBOODSCHAPPEN TE TONEN, MAAR OM TE KIJKEN OF HET EEN GELDIGE INDEX KAN ZIJN
                $query,
                [
                    "index" => [InputService::INTEGER, InputService::NOT_EMPTY]
                ]
            );
            if ($query) $bestelService->verwijderUitWinkelMandje((int)$query["index"]);
            SessionService::setSession("fromWinkelmandjeController", true); // OM DUIDELIJK TE MAKEN AAN DE BESTELCONTROLLER DAT ER HANDELINGEN ZIJN GEBEURD, IVM ERRORS
            break;
    }
}

// ALS NIET NAAR ERGENS ANDERS OMGELEID => TERUG NAAR BESTELLEN
RedirectService::redirectTo("bestelController.php");
