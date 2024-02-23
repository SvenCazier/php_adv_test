<?php
//bestelController.php
declare(strict_types=1);

require_once("bootstrap.php");

use App\Business\{BestelService, ProductService, KlantService, ErrorService, SessionService};

/* TOON BESTELFORMULIER EN WINKELMANDJE */

$klantService = new KlantService();
$bestelService = new BestelService();
$productService = new ProductService();

// NIET MEER NODIG OMDAT ERRORS AUTOMATISCH GECLEARED WORDEN BIJ HET TONEN, DUS KUNNEN NIET MEER BESTAAN BIJ NAVIGEREN NAAR ANDERE PAGINA

print $twig->render(
    "bestelTemplate.twig",
    array(
        "isAuthenticated" => $klantService->isAuthenticated(),
        "klant" => $klantService->getKlant(),
        "pizzas" => $productService->getAllPizzas(),
        "extras" => $productService->getAllExtras(),
        "winkelmandje" => $bestelService->getWinkelMandje(),
        "errors" => ErrorService::getErrors()
    )
);
