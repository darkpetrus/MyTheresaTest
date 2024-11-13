<?php

namespace App\Tests\unit\Backoffice\Products\Domain\Service;

use App\Backoffice\Products\Domain\Model\Product;
use App\Backoffice\Products\Domain\Service\DiscountCalculatorService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DiscountCalculatorServiceTest extends TestCase
{
    private DiscountCalculatorService $discountCalculatorService;

    protected function setUp(): void
    {
        $this->discountCalculatorService = new DiscountCalculatorService();
    }

   #[Test]
   #[DataProvider('severalProducts')]
    public function test_discount_calculator_service(string $sku, string $category, ?int $discount): void
    {
        $product = Product::createFromArray(
            [
                'sku' => $sku,
                'name' => 'a-product-name',
                'category' => $category,
                'price_min_unit' => 100000,
            ]
        );

        $response = $this->discountCalculatorService->getDiscountPercentage($product);
        $this->assertEquals($discount, $response);
    }


    public static function severalProducts(): array
    {
        return [
            'Category Discount' => ['sku123','boots', 30 ],
            'Only Sku Discount' => ['000003','sandals', 15 ],
            'Sku Discount but Category Discount' => ['000003','boots', 30 ],
            'No Discount' => ['sku123','sandals', null ],
        ];
    }

}