<?php
//App/Exceptions/InvalidLocationException.php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class InvalidLocationException extends Exception
{
    public function __construct(int $code = 0, Throwable $previous = null)
    {
        $message = "Er kan op dit adres niet geleverd worden.";
        parent::__construct($message, $code, $previous);
    }
}
