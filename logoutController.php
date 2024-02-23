<?php
//logoutController.php
declare(strict_types=1);

require_once("bootstrap.php");

use App\Business\{BestelService, KlantService, RedirectService};

$bestelService = new BestelService();
$klantService = new KlantService();

// HAAL KLANTGEGEVENS UIT WINKELMANDJE, MAAR BEWAAR HET NOG ZODAT KLANT ZICH EVENTUEEL MET EEN ANDER ACCOUNT KAN AANMELDEN ZONDER MANDJE TE VERLIEZEN
$bestelService->unsetKlant();

// OM TE VOORKOMEN DAT ALS KLANT WOU AFREKENEN, ZICH AFMELDT EN OPNIEUW AANMELDT AUTOMATISCH NAAR OVERZICHT GESTUURD WORDT
$bestelService->unsetKlaarOmAfTeRekenen();

$klantService->logout();
RedirectService::redirectTo("index.php");
