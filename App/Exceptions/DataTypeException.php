<?php
//App/Exceptions/DataTypeException.php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class DataTypeException extends Exception
{
    public function __construct(string $failedInput = "", array $dataTypes = [], int $code = 0, Throwable $previous = null)
    {
        $message = "Er werd een verkeerd type data ingevuld.";
        if ($failedInput && $dataTypes) {
            $failedInput = str_replace("_", " ", $failedInput);
            $dataTypesString = implode(', ', array_map(function ($dataType) {
                return $dataType;
            }, $dataTypes));
            $indexOfLastComma = strrpos($dataTypesString, ',');
            // voorkom dat "niet gevonden" return value als 0 geïnterpreteerd wordt
            // voorkom dat substr_replace() "false" krijgt als offset
            if ($indexOfLastComma) {
                $dataTypesString = substr_replace($dataTypesString, ' of', $indexOfLastComma, 1);
            }
            $message = sprintf("Gelieve voor \"%s\" een %s in te vullen.", $failedInput, $dataTypesString);
        }
        parent::__construct($message, $code, $previous);
    }
}
