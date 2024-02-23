<?php
//App/Exceptions/InvalidProductIdException.php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class InvalidProductIdException extends Exception
{
    public function __construct(int $code = 0, Throwable $previous = null)
    {
        $message = "De door u gekozen producten bestaan niet.";
        parent::__construct($message, $code, $previous);
    }
}
