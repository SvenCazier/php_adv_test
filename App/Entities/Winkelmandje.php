<?php
//App/Entities/WinkelMandje.php
declare(strict_types=1);

namespace App\Entities;

class WinkelMandje
{
    private array $bestelLijnen = [];
    private int $bestelLijnTeller = 1; // OMDAT DE QUERY PAREMETER BIJ HET VERWIJDEREN NIET 0 KAN ZIJN
    private ?Klant $klant = null;

    public function voegBestelLijnToe(Pizza $pizza, int $aantal): void
    {
        $this->bestelLijnen[$this->bestelLijnTeller] = ["pizza" => $pizza, "aantal" => $aantal];
        $this->bestelLijnTeller++;
    }

    public function getbestelLijnen(): array
    {
        return $this->bestelLijnen;
    }

    public function verwijderbestelLijn(int $index): void
    {
        unset($this->bestelLijnen[$index]);
    }

    public function getKlant(): ?Klant
    {
        return $this->klant;
    }

    public function setKlant(?Klant $klant): void
    {
        $this->klant = $klant;
    }

    public function unsetKlant(): void
    {
        $this->klant = null;
    }

    public function getSubTotaal(int $index): float
    {
        $bestelLijn = $this->bestelLijnen[$index];
        $pizza = $bestelLijn["pizza"];
        $subtotaal = $pizza->getPrijsVoorKlant($this->klant);
        foreach ($pizza->getExtras() as $extra) {
            $subtotaal += $extra->getPrijsVoorKlant($this->klant);
        }
        return $subtotaal * $bestelLijn["aantal"];
    }

    public function getTotaal(): float
    {
        $totaal = 0;
        foreach (array_keys($this->bestelLijnen) as $index) {
            $totaal += $this->getSubTotaal($index);
        }
        return $totaal;
    }

    public function isLeeg(): bool
    {
        if (empty($this->bestelLijnen)) return true;
        return false;
    }
}
