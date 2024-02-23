<?php
//App/Business/SessionService.php
declare(strict_types=1);

namespace App\Business;

class CookieService
{
    public static function setCookie($key, $value, $exp)
    {
        setcookie($key, $value, $exp);
    }
    public static function getCookie($key): ?string
    {
        return $_COOKIE[$key] ?? null;
    }
}
