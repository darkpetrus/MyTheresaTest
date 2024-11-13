<?php

namespace App\Backoffice\Products\Domain\Service;

use App\Backoffice\Products\Domain\Model\Product;
use App\Backoffice\Products\Domain\ValueObject\ProductCategory;

class DiscountCalculatorService
{
    // Configure discounts by SKU
    private const FIXED_SKU_DISCOUNT = [
        '000003' => 15
    ];

    public function getDiscountPercentage(Product $product): ?int
    {
        $categoryDiscount = match (ProductCategory::fromString($product->category())) {
            ProductCategory::BOOTS => 30,
            default => null,
        };

        $fixedSkuDiscount = self::FIXED_SKU_DISCOUNT[$product->sku()] ?? null;


        if ($categoryDiscount !== null || $fixedSkuDiscount !== null) {
            return max($categoryDiscount ?? 0, $fixedSkuDiscount ?? 0);
        }

        return null;
    }
}