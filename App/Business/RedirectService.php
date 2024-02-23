<?php
//App/Business/RedirectService.php
declare(strict_types=1);

namespace App\Business;


class RedirectService
{
    public static function redirectTo(string $url): void
    {
        header("Location: $url");
        exit();
    }

    public static function redirectToPreviousOrDefault(string $defaultURL): void
    {
        $previousURL = $_SERVER['HTTP_REFERER'];
        $currentURL = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];;
        if ($previousURL && $previousURL !== $currentURL) {
            header("Location: $previousURL");
        } else {
            header("Location: $defaultURL");
        }
        exit();
    }
}
