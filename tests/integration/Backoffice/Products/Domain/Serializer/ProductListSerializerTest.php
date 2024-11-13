<?php

namespace App\Tests\integration\Backoffice\Products\Domain\Serializer;

use App\Backoffice\Products\Domain\Model\Product;
use App\Backoffice\Products\Domain\Serializer\ProductListSerializer;
use App\Backoffice\Products\Domain\Serializer\ProductSerializer;
use App\Backoffice\Products\Domain\Service\DiscountCalculatorService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ProductListSerializerTest extends TestCase
{
    private ProductListSerializer $serializer;

    protected function setUp(): void
    {
        $this->serializer = new ProductListSerializer(
            new ProductSerializer(
                new DiscountCalculatorService()
            )
        );
    }

    #[Test]
    #[DataProvider('productList')]
    public function testSerializeProductWithoutDiscount(
        string $sku,
        string $name,
        string $category,
        int $priceMinUnit,
        int $finalPrice,
        ?string $discount
    )
    {
        $product = Product::createFromArray(
            [
               'sku' => $sku,
                'name' => $name,
                'category' => $category,
                'price_min_unit' =>$priceMinUnit
            ]
        );
        $serializedProduct = $this->serializer->serialize([$product]);

        $this->assertCount(1, $serializedProduct);
        $this->assertEquals($priceMinUnit, $serializedProduct[0]['price']['original']);
        $this->assertEquals($finalPrice, $serializedProduct[0]['price']['final']);
        $this->assertEquals($discount, $serializedProduct[0]['price']['discount_percentage']);
    }

    public static function productList(): array
    {
        return [
            'Product without discount' => ['000001', 'Test Product', 'electric', 89000, 89000, null],
            'Product with 15% discount' => ['000003', 'Another Product', 'electric', 89000, 75650, '15%'],
            'Product with 30% discount' => ['000001', 'One More Product', 'boots', 89000, 62300, '30%'],
            'Product with category and Sku discount' => ['000003', 'Yet Another Test Product', 'boots', 100000, 70000, '30%'],
        ];
    }

}