<?php

namespace App\Backoffice\Products\Domain\Repository;

use App\Backoffice\Products\Domain\Model\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    public function delete(Product $product): void;
}