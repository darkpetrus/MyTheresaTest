<?php

namespace App\Backoffice\Products\Domain\Service;

use App\Backoffice\Products\Domain\Model\Product;

class CreateNewProductService
{
    public function execute(
        string $sku,
        string $name,
        int    $priceMinUnit,
        string $category
    ): Product {

       return Product::createFromArray([
            'name' => $name,
            'price_min_unit' => $priceMinUnit,
            'category' => $category,
            'sku' => $sku,
        ]);
    }
}