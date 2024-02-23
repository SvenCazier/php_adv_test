<?php
//App/Exceptions/EmptyInputException.php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class EmptyInputException extends Exception
{
    public function __construct(string $failedInput = "", int $code = 0, Throwable $previous = null)
    {
        $message = "Gelieve dit veld in te vullen.";
        if ($failedInput) {
            $failedInput = str_replace("_", " ", $failedInput);
            $message = sprintf("Gelieve \"%s\" in te vullen.", $failedInput);
        }
        parent::__construct($message, $code, $previous);
    }
}
