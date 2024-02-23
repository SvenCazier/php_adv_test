<?php
//App/Exceptions/GeenKlantGegevens.php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class GeenKlantGegevensException extends Exception
{
    public function __construct(int $code = 0, Throwable $previous = null)
    {
        $message = "Winkelmandje niet gevonden.";
        parent::__construct($message, $code, $previous);
    }
}
