<?php
//registreerController.php
declare(strict_types=1);

require_once("bootstrap.php");

use App\Business\{
    BestelService,
    KlantService,
    InputService,
    RedirectService,
    RequestService,
    ErrorService
};

/* TOON FORMULIER VOOR EMAIL EN WACHTWOORD EN REGISTREER GEBRUIKER */
/* NA REGISTRATIE => LOG IN */

ErrorService::clearErrors();
$klantService = new KlantService();
$bestelService = new BestelService();

// ALS REEDS IS INGELOGD OF NIET KLAAR OM AF TE REKEKEN, OF GEEN ADRESGEGEVENS INGEVULD HOORT KLANT HIER NIET TE ZIJN
if ($klantService->isAuthenticated() || !$bestelService->isKlaarOmAfTeRekeken()) RedirectService::redirectTo("index.php");
else if (!$bestelService->getKlant()) RedirectService::redirectTo("adresController.php");

if (RequestService::isPost()) {
    $inputs = RequestService::postArray();
    if (InputService::passwordsEqual($inputs["wachtwoord"], $inputs["herhaal_wachtwoord"])) {

        $inputs = InputService::validateInputs(
            $inputs,
            [
                "email" => [InputService::TEXT, InputService::NOT_EMPTY],
                "wachtwoord" => [InputService::PASSWORD, InputService::NOT_EMPTY],
                "herhaal_wachtwoord" => [InputService::PASSWORD, InputService::NOT_EMPTY],
            ]
        );

        if ($inputs) {
            if ($klantService->registreer($inputs["email"], $inputs["wachtwoord"], $bestelService->getKlant())) {
                // REGISTRATIE GELUKT
                if ($klantService->isAuthenticated()) RedirectService::redirectTo("overzichtController.php");
                RedirectService::redirectTo("loginController.php");
            }
        }
    }
}

print $twig->render(
    "registreerTemplate.twig",
    array(
        "post" => RequestService::postArray(),
        "errors" => ErrorService::getErrors()
    )
);
