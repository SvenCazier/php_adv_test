<?php
//App/Exceptions/EmailExistsException.php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class EmailExistsException extends Exception
{
    public function __construct(int $code = 0, Throwable $previous = null)
    {
        $message = "Dit e-mailadres is reeds geregristeerd, gelieve aan te melden of een ander e-mailadres te gebruiken om een nieuw account te maken.";
        parent::__construct($message, $code, $previous);
    }
}
