<?php

namespace App\Backoffice\Products\Domain\Repository;

use App\Backoffice\Products\Domain\Model\Product;

interface ProductSearchRepositoryInterface
{
    public function findAllByCategoryAndPrice(?string $category, ?int $priceLessThan): array;
    public function findBySku(string $sku): ?Product;
}