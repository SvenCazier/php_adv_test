<?php
//App/Entities/Product.php
declare(strict_types=1);

namespace App\Entities;

class Product
{
    private int $id;
    private string $naam;
    private string $omschrijving;
    private float $prijs;
    private float $promotiePct;

    public function __construct(int $id, string $naam, string $omschrijving, float $prijs, float $promotiePct)
    {
        $this->id = $id;
        $this->naam = $naam;
        $this->omschrijving = $omschrijving;
        $this->prijs = $prijs;
        $this->promotiePct = $promotiePct;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNaam(): string
    {
        return $this->naam;
    }

    public function getOmschrijving(): string
    {
        return $this->omschrijving;
    }

    public function getPrijs(): float
    {
        return $this->prijs;
    }

    public function getPromotiePrijs(): float
    {
        return round($this->prijs - ($this->prijs * $this->promotiePct), 2);
    }

    public function inPromotie(): bool
    {
        return (bool) $this->promotiePct;
    }

    public function getPrijsVoorKlant(Klant|null $klant): float
    {
        if ($klant && $klant->heeftRechtOpPromotie()) {
            return $this->getPromotiePrijs();
        }
        return $this->getPrijs();
    }
}
