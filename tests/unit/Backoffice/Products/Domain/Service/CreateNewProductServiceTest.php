<?php
namespace App\Tests\unit\Backoffice\Products\Domain\Service;

use App\Backoffice\Products\Domain\Model\Product;
use App\Backoffice\Products\Domain\Service\CreateNewProductService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CreateNewProductServiceTest extends TestCase
{
    private CreateNewProductService $createNewProductService;

    protected function setUp(): void
    {
        $this->createNewProductService = new CreateNewProductService();
    }

    #[Test]
    public function should_create_product()
    {
        $sku = '12345';
        $name = 'Boots';
        $priceMinUnit = 10000;
        $category = 'BOOTS';

        $product = $this->createNewProductService->execute($sku, $name, $priceMinUnit, $category);
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($sku, $product->sku());
        $this->assertEquals($name, $product->name());
        $this->assertEquals($category, $product->category());
    }
}
