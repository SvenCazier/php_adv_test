<?php
//App/Business/RequestService.php
declare(strict_types=1);

namespace App\Business;

class RequestService
{
    public static function getKey(string $key): ?string
    {
        return $_GET[$key] ?? null;
    }

    public static function postKey(string $key): ?string
    {
        return $_POST[$key] ?? null;
    }

    public static function getArray(): array
    {
        return $_GET;
    }

    public static function postArray(): array
    {
        return $_POST;
    }

    public static function isGet(): bool
    {
        return $_SERVER["REQUEST_METHOD"] === "GET";
    }

    public static function isPost(): bool
    {
        return $_SERVER["REQUEST_METHOD"] === "POST";
    }
}
