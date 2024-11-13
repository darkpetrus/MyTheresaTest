<?php

namespace App\Backoffice\Products\Domain\Serializer;

use App\Backoffice\Products\Domain\Model\Product;
use App\Backoffice\Products\Domain\Service\DiscountCalculatorService;

class ProductSerializer
{
    public function __construct(
        private readonly DiscountCalculatorService $discountCalculatorService
    )
    {
    }

    public function serialize(?Product $product): array
    {
        if ($product === null) {
            return [];
        }
        $finalPrice = $product->priceMinUnit();
        $discount = $this->discountCalculatorService->getDiscountPercentage($product);

        if($discount !== null){
            $finalPrice = $product->priceMinUnit() - ($product->priceMinUnit() * ($discount / 100));
        }

        return [
            'sku' => $product->sku(),
            'name' => $product->name(),
            'category' => $product->category(),
            'price' => [
                'original' => $product->priceMinUnit(),
                'final' => $finalPrice,
                'discount_percentage' => $discount ? ($discount .'%') : null,
                'currency' => $product->currency()
            ]
        ];
    }
}