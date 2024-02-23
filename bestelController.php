<?php
//bestelController.php
declare(strict_types=1);

require_once("bootstrap.php");

use App\Business\{BestelService, ProductService, KlantService, ErrorService, SessionService};

/* TOON BESTELFORMULIER EN WINKELMANDJE */

$klantService = new KlantService();
$bestelService = new BestelService();
$productService = new ProductService();

// OMDAT ALLE HANDELINGEN IN DE WINKELMANDJECONTROLLER GEBEUREN KUNNEN ERRORS HIER NIET ZOMAAR GEWIST WORDEN
// MAAR ALS DIRECT VAN EEN ANDERE PAGINA NAAR HIER WORDT GENAVIGEERD ZONDER EVENTUELE ERRORS OP TE LOSSEN ZOUDEN DIE HIER GETOOND WORDEN

if (SessionService::getSession("fromWinkelmandjeController")) SessionService::clearSessionVariable("fromWinkelmandjeController");
else ErrorService::clearErrors(); // ZIJN NIET HIER GEKOMEN DOOR HANDELINGEN IN WINKELMANDJE CONTROLLER => VERWIJDER ERRORS


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
