<?php
//App/Business/SessionService.php
declare(strict_types=1);

namespace App\Business;

session_start();

class SessionService
{
    public static function setSession(string $key, mixed $value, bool $wantsToBeSerialized = false): void
    {
        if (self::needsToBeSerialized($value) || $wantsToBeSerialized) {
            $value = serialize($value);
        }
        $_SESSION[$key] = $value;
    }

    public static function getSession(string $key): mixed
    {
        $value = $_SESSION[$key] ?? null;
        if ($value && self::isSerialized($value)) {
            $value = unserialize($value);
        }
        return $value;
    }

    public static function clearSessionVariable(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function desroySession(): void
    {
        $_SESSION = [];
    }

    private static function needsToBeSerialized(mixed $value): bool
    {
        if (is_object($value) || (is_array($value) && count($value) > 0 && is_object($value[key($value)]))) {
            return true;
        }
        return false;
    }

    private static function isSerialized(mixed $value): bool
    {
        if (is_string($value) && !empty($value)) {
            $value = trim($value);
            if ($value === 'N;' || $value === 'b:0;' || @unserialize($value) !== false) {
                return true;
            }
        }
        return false;
    }
}
