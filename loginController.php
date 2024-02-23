<?php
//loginController.php
declare(strict_types=1);

require_once("bootstrap.php");

use App\Business\{
    ErrorService,
    InputService,
    RedirectService,
    RequestService,
    KlantService,
    BestelService,
    SessionService
};

$klantService = new KlantService();

ErrorService::clearErrors();

if (RequestService::isPost()) {
    $validatedInputs = InputService::validateInputs(
        RequestService::postArray(),
        [
            "email" => [InputService::NOT_EMPTY, InputService::EMAIL],
            "wachtwoord" => [InputService::NOT_EMPTY, InputService::PASSWORD]
        ]
    );
    if ($validatedInputs) $klantService->login($validatedInputs["email"], $validatedInputs["wachtwoord"]);
}

if ($klantService->isAuthenticated()) {
    $bestelService = new BestelService();
    $bestelService->setKlant($klantService->getKlant());
    // KLANT KAN AANMELDEN TIJDENS HET BESTELLEN MAAR WIL DAAROM NOG NIET AFREKENEN
    // HEEFT ZICH AANGEMELD, HEEFT EEN WINKELMANDJE EN IS NAAR LOGIN DOORVERWEZEN DOOR TE WILLEN AFREKENEN
    if ($bestelService->isKlaarOmAfTeRekeken()) RedirectService::redirectTo("overzichtController.php");
    // HEEFT ZICH AANGEMELD MAAR HEEFT GEEN WINKELMANDJE OF HEEFT NOG NIET AANGEGEVEN TE WILLEN AFREKENEN
    RedirectService::redirectTo("bestelController.php");
}

print $twig->render(
    "loginTemplate.twig",
    array(
        "post" => RequestService::postArray(),
        "email" => $klantService->getEmail(), // UIT COOKIE
        "errors" => ErrorService::getErrors()
    )
);
