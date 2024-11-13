<?php

namespace App\Tests\integration\Backoffice\Products\Infrastructure\Persistence;

use App\Backoffice\Products\Domain\Model\Product;
use App\Backoffice\Products\Infrastructure\Persistence\DoctrineProductRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineProductRepositoryTest extends KernelTestCase
{
    private DoctrineProductRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(DoctrineProductRepository::class);
    }

    #[Test]
    public function should_save_find_product_and_delete(): void
    {
        $product = Product::createFromArray([
            'sku' => '0000000',
            'name' => 'Test Product',
            'category' => 'Test Category',
            'price_min_unit' => 10000,
        ]);

        // Save the product
        $this->repository->save($product);

        // Retrieve the product by ID
        $retrievedProduct = $this->repository->findById($product->id());

        // Assertions
        $this->assertInstanceOf(Product::class, $retrievedProduct);
        $this->assertEquals('0000000', $retrievedProduct->sku());
        $this->assertEquals('Test Product', $retrievedProduct->name());
        $this->assertIsInt( $retrievedProduct->id());

        // Save ID for check
        $productId = $retrievedProduct->id();

        // Delete the product
        $this->repository->delete($retrievedProduct);

        // Confirm deletion
        $deletedProduct = $this->repository->findById($productId);
        $this->assertNull($deletedProduct);
    }
}