<?php
//App/Exceptions/LessThanOrEqualToZeroException.php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class LessThanOrEqualToZeroException extends Exception
{
    public function __construct(int $code = 0, Throwable $previous = null)
    {
        $message = "Mag niet kleiner dan of gelijk aan 0 zijn.";
        parent::__construct($message, $code, $previous);
    }
}
