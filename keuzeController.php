<?php
//keuzeController.php
declare(strict_types=1);

require_once("bootstrap.php");

use App\Business\{
    RedirectService,
    BestelService,
    KlantService
};

/* TOON KEUZE OM AAN TE MELDEN OF ADRES IN TE VULLEN */

// NIET TOEGANKELIJK WANNEER WINKELMANDJE LEEG IS
$bestelService = new BestelService();
if (!$bestelService->getWinkelMandje()) RedirectService::redirectTo("index.php");

// ALS AANGEMELD AUTOMATISCH DOORSTUREN
$klantService = new KlantService();
if ($klantService->isAuthenticated()) RedirectService::redirectTo("overzichtController.php");

print $twig->render("keuzeTemplate.twig");
