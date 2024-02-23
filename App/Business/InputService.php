<?php
//App/Business/InputService.php
declare(strict_types=1);

namespace App\Business;

use App\Business\ErrorService;
use App\Exceptions\{DataTypeException, EmptyInputException, LessThanOrEqualToZeroException, PasswordsNotEqualException};

// WAARSCHIJNLIJK INGEWIKKELDER DAN HET HOEFT TE ZIJN

class InputService
{

    const EMAIL = "email";
    const NUMBER = "nummer";
    const TEXT = "tekst";
    const PASSWORD = "wachtwoord";
    const BOOLEAN = "ja/nee waarde";
    const INTEGER = "geheel getal";
    const FLOAT = "decimaal getal";
    const HOUSE_NUMBER = "huisnummer";
    const PHONE_NUMBER = "telefoonnummer";
    const NOT_EMPTY = "not empty";
    const NOT_LESS_THAN_OR_EQUAL_TO_ZERO = "<=0";
    const IS_ARRAY = "array"; // OM TE CHECKEN OF ARRAYS OOK ARRAYS ZIJN

    public static function validateInputs(array $inputs, array $keys): ?array
    {
        if (empty($keys)) return null;
        // EERST SANITIZE OMDAT DAAR DE TRIM GEBEURD EN INPUT MET ENKEL SPATIES "LEEG" IS
        // ANDERS EXTRA LOOP OM TE TRIMMEN
        $inputs = self::sanitizeInput($inputs);
        if (self::emptyCheckFailed($inputs, $keys)) return null; // CHECK ALLE INPUT OP EMPTY INDIEN NODIG
        if (self::arrayCheckFailed($inputs, $keys)) return null; // CHECK OF WAT EEN ARRAY MOET ZIJN EEN ARRAY IS EN OMGEKEERD
        if (self::greaterThanZeroFailed($inputs, $keys)) return null; // CHECK OF WAT STRICT POSITIEF MOET ZIJN HET OOK IS


        // CHECK ALLE INPUT OP GELDIG DATATYPE ZODAT ER VOOR IEDERE INPUT EEN ERROR KAN GEMAAKT WORDEN INDIEN NODIG
        $areDataTypesValid = true;
        foreach ($keys as $key => $dataTypes) {
            // VERWIJDER NOT_EMPTY EN IS_ARRAY UIT ARRAY, WANT GEEN DATATYPE EN REEDS GECONTROLEERD
            $dataTypes = array_filter($dataTypes, function ($dataType) {
                return $dataType !== self::NOT_EMPTY && $dataType !== self::IS_ARRAY;
            });

            if (is_array($inputs[$key])) {
                foreach ($inputs[$key] as $input) {
                    if (self::isInvalidDataType($key, $input, $dataTypes)) $areDataTypesValid = false;
                }
            } else {
                if (self::isInvalidDataType($key, $inputs[$key], $dataTypes)) $areDataTypesValid = false;
            }
        }
        // ALS IEDERE TE CHECKEN INPUT GECHECKT IS
        if ($areDataTypesValid) return $inputs;
        return null;
    }

    public static function passwordsEqual(string $wachtwoord1, string $wachtwoord2): bool
    {
        try {
            if ($wachtwoord1 !== $wachtwoord2) throw new PasswordsNotEqualException();
            return true;
        } catch (PasswordsNotEqualException $e) {
            ErrorService::setError($e->getMessage());
            return false;
        }
    }

    public static function convertStringArrayToIntArray(array $array): array
    {
        return array_map('intval', $array);
    }

    private static function sanitizeInput(array $inputs): array
    {
        foreach ($inputs as $key => $value) {
            if (is_array($inputs[$key])) {
                foreach ($inputs[$key] as $arrKey => $arrValue) {
                    $inputs[$key][$arrKey] = stripslashes(htmlspecialchars(trim($arrValue ?? ""), ENT_QUOTES));
                }
            } else {
                $inputs[$key] = stripslashes(htmlspecialchars(trim($value ?? ""), ENT_QUOTES));
            }
        }
        return $inputs;
    }

    private static function emptyCheckFailed(array $inputs, array $keys): bool
    {
        $isValid = true;
        foreach ($keys as $key => $value) {
            if (!array_key_exists($key, $inputs)) return true;
            try {
                if (empty($inputs[$key]) && in_array(self::NOT_EMPTY, $value)) { // EMPTY INPUT MAAR MAG NIET EMPTY ZIJN
                    throw new EmptyInputException($key);
                }
            } catch (EmptyInputException $e) {
                ErrorService::setError($e->getMessage(), $key);
                $isValid = false;
            }
        }
        return !$isValid;
    }

    private static function arrayCheckFailed(array $inputs, array $keys): bool
    {
        $isValid = true;
        foreach ($keys as $key => $value) {
            if (!array_key_exists($key, $inputs)) return true;
            try {
                if ((is_array($inputs[$key]) xor in_array(self::IS_ARRAY, $value))) { // IS ARRAY MAAR MAG GEEN ARRAY ZIJN OF OMGEKEERD
                    throw new DataTypeException();
                }
            } catch (DataTypeException $e) {
                ErrorService::setError($e->getMessage(), $key);
                $isValid = false;
            }
        }
        return !$isValid;
    }

    private static function greaterThanZeroFailed(array $inputs, array $keys): bool
    {
        $isValid = true;
        foreach ($keys as $key => $value) {
            try {
                if ($inputs[$key] <= 0 && in_array(self::NOT_LESS_THAN_OR_EQUAL_TO_ZERO, $value)) {
                    throw new LessThanOrEqualToZeroException();
                }
            } catch (LessThanOrEqualToZeroException $e) {
                ErrorService::setError($e->getMessage(), $key);
                $isValid = false;
            }
        }
        return !$isValid;
    }

    private static function isInvalidDataType($key, $input, $dataTypes): bool // KAN MEEDERE DATATYPES PER INPUT AANVAARDEN
    {
        try {
            if (empty($dataTypes)) return false; // ALS GEEN DATATYPES OPGEGEVEN => AUTOMATISCH VALID INPUT
            foreach ($dataTypes as $dataType) {
                if (match ($dataType) {
                    self::BOOLEAN => filter_var($input, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) === null ? false : true,
                    self::EMAIL => filter_var($input, FILTER_VALIDATE_EMAIL),
                    self::NUMBER => is_numeric($input),
                    self::FLOAT => self::isFloat($input),
                    self::INTEGER => self::isInt($input),
                    self::TEXT => is_string($input),
                    self::PASSWORD => is_string($input),
                    self::HOUSE_NUMBER => self::isHouseNumber($input),
                    self::PHONE_NUMBER => self::isPhoneNumber($input),
                }) {
                    return false; // ALS DATATYPES DATATYPES OPGEGEVEN => ALS 1 MATCH => VALID INPUT
                }
            }
            // ALS GEEN RETURN IN FOREACH => GEEN MATCH GEVONDEN
            throw new DataTypeException($key, $dataTypes);
        } catch (DataTypeException $e) {
            ErrorService::setError($e->getMessage(), $key);
            return true;
        }
    }

    private static function isInt(string $input): bool
    {
        if (is_numeric($input)) {
            return is_int(1 * $input);
        }
        return false;
    }

    private static function isFloat(string $input): bool
    {
        if (is_numeric($input)) {
            return is_float(1 * $input);
        }
        return false;
    }

    private static function isHouseNumber(string $input): bool
    {
        $pattern = '/^\d+\w{1}$|^\d+\/\d{1,4}$/'; // KAN BETER, MAAR IK DENK DAT DIT DE MEESTE HUISNUMMERS WEL DEKTs
        return preg_match($pattern, $input) === 1;
    }

    private static function isPhoneNumber(string $input): bool
    {
        if (self::isInt($input) && strlen($input) > 8 && strlen($input) < 11) {
            return true;
        }
        return false;
    }
}
