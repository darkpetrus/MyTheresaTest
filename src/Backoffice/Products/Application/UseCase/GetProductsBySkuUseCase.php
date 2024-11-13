<?php

namespace App\Backoffice\Products\Application\UseCase;


use App\Backoffice\Products\Domain\Repository\ProductSearchRepositoryInterface;
use App\Backoffice\Products\Domain\Serializer\ProductSerializer;

class GetProductsBySkuUseCase
{
    public function __construct(
        private readonly ProductSearchRepositoryInterface $repository,
        private readonly ProductSerializer $serializer
    )
    {
    }

    function execute(?string $sku = null): array
    {
        $products = $this->repository->findBySku($sku);
        return $this->serializer->serialize($products);
    }
}