<?php
//App/Entities/Pizza.php
declare(strict_types=1);

namespace App\Entities;

class Pizza extends Product
{
    private array $extras = [];

    public function setExtra(Extra $extra): void
    {
        $this->extras[] = $extra;
    }

    public function getExtras(): array
    {
        return $this->extras;
    }
}
