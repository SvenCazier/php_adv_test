<?php
//successController.php
declare(strict_types=1);

require_once("bootstrap.php");

use App\Business\{SessionService, RedirectService, KlantService, BestelService};

if (!SessionService::getSession("success")) RedirectService::redirectTo("index.php");
$klantService = new KlantService();
$bestelService = new BestelService();

SessionService::clearSessionVariable("success");

print $twig->render(
    "successTemplate.twig",
    array(
        "isAuthenticated" => $klantService->isAuthenticated()
    )
);
