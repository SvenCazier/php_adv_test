<?php
//overzichtController.php
declare(strict_types=1);

require_once("bootstrap.php");

use App\Business\{BestelService, KlantService, RedirectService, RequestService, ErrorService, SessionService};

/* TOON OVERZICHT BESTELLING EN VERWERK */

$klantService = new KlantService();
$bestelService = new BestelService();

// ALS KLANT NIET KLAAR IS OM AF TE REKENEN HOORT KLANT HIER NIET TE ZIJN
if (!$bestelService->isKlaarOmAfTeRekeken()) RedirectService::redirectTo("index.php");

if (RequestService::isGet() && RequestService::getKey("action") === "order") {
    if ($bestelService->afrekenen()) {
        SessionService::setSession("success", true);
        RedirectService::redirectTo("successController.php");
    }
}

print $twig->render(
    "overzichtTemplate.twig",
    array(
        "isAuthenticated" => $klantService->isAuthenticated(),
        "klant" => $bestelService->getKlant(),
        "winkelmandje" => $bestelService->getWinkelMandje(),
        "errors" => ErrorService::getErrors()
    )
);
