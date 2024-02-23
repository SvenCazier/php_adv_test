<?php
//App/Business/ErrorService.php
declare(strict_types=1);

namespace App\Business;

use App\Business\SessionService;

class ErrorService
{
    public static function setError($errormsg, $key = null): void
    {
        $errors = self::getErrors();
        if ($key) $errors[$key] = $errormsg;
        else $errors[] = $errormsg;

        SessionService::setSession("errors", $errors);
    }

    public static function getErrors(): array
    {
        $errors = SessionService::getSession("errors") ?? [];
        // Dit had ik toch al geïmplementeerd, maar zat niet in de originele versie
        if ($errors) {
            self::clearErrors();
        }
        return $errors;
    }

    public static function clearErrors(): void
    {
        SessionService::clearSessionVariable("errors");
    }
}
