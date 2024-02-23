<?php
//App/Business/ProductService.php
declare(strict_types=1);

namespace App\Business;

use App\Business\{ErrorService};
use App\Data\ProductDAO;
use App\Entities\Product;
use Exception;

class ProductService
{
    private ProductDAO $productDAO;

    public function __construct()
    {
        $this->productDAO = new ProductDAO();
    }

    public function getAllPizzas(): array
    {
        return $this->getAllProductenOfType(1);
    }
    public function getAllExtras(): array
    {
        return $this->getAllProductenOfType(2);
    }

    private function getAllProductenOfType(int $type): array
    {
        $producten = [];
        try {
            $result = $this->productDAO->getAllProductenByType($type);
            foreach ($result as $product) {
                // HIER ONDERSCHEID TUSSEN PIZZA EN EXTRA NIET NODIG
                $producten[] = new Product(
                    $product["id"],
                    $product["naam"],
                    $product["omschrijving"],
                    $product["prijs"],
                    $product["promotiePct"]
                );
            }
        } catch (Exception $e) {
            ErrorService::setError($e->getMessage());
        }
        return $producten;
    }
}
