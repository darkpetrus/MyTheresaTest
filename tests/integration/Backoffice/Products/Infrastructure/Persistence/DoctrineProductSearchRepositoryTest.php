<?php

namespace App\Tests\integration\Backoffice\Products\Infrastructure\Persistence;

use App\Backoffice\Products\Domain\Model\Product;
use App\Backoffice\Products\Domain\ValueObject\ProductCategory;
use App\Backoffice\Products\Infrastructure\Persistence\DoctrineProductSearchRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineProductSearchRepositoryTest extends KernelTestCase
{
    private DoctrineProductSearchRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(DoctrineProductSearchRepository::class);
    }

    #[Test]
    public function should_find_all_by_non_exist_category_and_price_returns_empty(): void
    {
        $category = 'Electronics';
        $priceLessThan = 20000;

        $products = $this->repository->findAllByCategoryAndPrice($category, $priceLessThan);

        $this->assertIsArray($products);
        $this->assertContainsOnlyInstancesOf(Product::class, $products);

        $this->assertCount(0, $products);
    }

    #[Test]
    public function should_find_by_sku_returns_correct_product(): void
    {
        $sku = '000001';
        $product = $this->repository->findBySku($sku);
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($sku, $product->sku());
    }

    #[Test]
    public function should_find_by_category_and_returns_products(): void
    {
        $category = ProductCategory::BOOTS->value;
        $products = $this->repository->findAllByCategoryAndPrice($category, null);

        $this->assertIsArray($products);
        $this->assertContainsOnlyInstancesOf(Product::class, $products);
        $this->assertCount(3, $products);
    }
}