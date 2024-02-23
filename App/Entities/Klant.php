<?php
//App/Entities/Klant.php
declare(strict_types=1);

namespace App\Entities;

class Klant
{
    private int $id;
    private string $naam;
    private string $voornaam;
    private Adres $adres;
    private string $telefoon;
    private string $email;
    private bool $promotie;

    public function __construct(
        int $id,
        string $naam,
        string $voornaam,
        Adres $adres,
        string $telefoon,
        bool $promotie,
        string $email = ""
    ) {
        $this->id = $id;
        $this->naam = $naam;
        $this->voornaam = $voornaam;
        $this->adres = $adres;
        $this->telefoon = $telefoon;
        $this->email = $email;
        $this->promotie = $promotie;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNaam(): string
    {
        return $this->naam;
    }

    public function getVoornaam(): string
    {
        return $this->voornaam;
    }

    public function getAdres(): Adres
    {
        return $this->adres;
    }

    public function getTelefoon(): string
    {
        return $this->telefoon;
    }

    public function getFormattedTelefoon(): string
    {
        switch (strlen($this->telefoon)) {
            case 10:
                return preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})/", "$1 $2 $3 $4", $this->telefoon);
                break;
            case 9:
                if (preg_match("/^(02|03|04|09)/", $this->telefoon)) {
                    return preg_replace("/(\d{2})(\d{3})(\d{2})(\d{2})/", "$1 $2 $3 $4", $this->telefoon);
                } else {
                    return preg_replace("/(\d{3})(\d{2})(\d{2})(\d{2})/", "$1 $2 $3 $4", $this->telefoon);
                }
                break;
            default:
                // VOOR HET GEVAL DAT ER FOUTE DATA IN DE DATABASE IS GESLOPEN
                return "";
        }
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function heeftRechtOpPromotie(): bool
    {
        return $this->promotie;
    }
}
