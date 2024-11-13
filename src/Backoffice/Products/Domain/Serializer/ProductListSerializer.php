<?php

namespace App\Backoffice\Products\Domain\Serializer;

use App\Backoffice\Products\Domain\Model\Product;

class ProductListSerializer
{
    public function __construct(
        private readonly ProductSerializer $productSerializer
    )
    {
    }

    /**
     * @param Product[] $products
     * @return array
     */
    public function serialize(array $products): array
    {
        return array_map(fn(Product $product) => $this->productSerializer->serialize($product), $products);
    }
}