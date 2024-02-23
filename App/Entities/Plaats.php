<?php
//App/Entities/Plaats.php
declare(strict_types=1);

namespace App\Entities;

class Plaats
{
    private int $id;
    private int $postcode;
    private string $woonplaats;

    public function __construct(int $id, int $postcode, string $woonplaats)
    {
        $this->id = $id;
        $this->postcode = $postcode;
        $this->woonplaats = $woonplaats;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPostcode(): int
    {
        return $this->postcode;
    }

    public function getWoonplaats(): string
    {
        return $this->woonplaats;
    }
}
