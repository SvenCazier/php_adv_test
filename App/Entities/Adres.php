<?php
//App/Entities/Adres.php
declare(strict_types=1);

namespace App\Entities;

class Adres
{
    private string $straat;
    private string $huisnummer;
    private Plaats $plaats;

    public function __construct(
        string $straat,
        string $huisnummer,
        Plaats $plaats
    ) {
        $this->straat = $straat;
        $this->huisnummer = $huisnummer;
        $this->plaats = $plaats;
    }

    public function getStraat(): string
    {
        return $this->straat;
    }
    public function getHuisnummer(): string
    {
        return $this->huisnummer;
    }
    public function getPlaats(): Plaats
    {
        return $this->plaats;
    }
}
