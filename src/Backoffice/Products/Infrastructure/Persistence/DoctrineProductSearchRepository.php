<?php

namespace App\Backoffice\Products\Infrastructure\Persistence;

use App\Backoffice\Products\Domain\Model\Product;
use App\Backoffice\Products\Domain\Repository\ProductSearchRepositoryInterface;
use App\Shared\Infrastructure\DbConnection;

class DoctrineProductSearchRepository implements ProductSearchRepositoryInterface
{
    private const DEFAULT_LIMIT = 5;

    public function __construct(
        private readonly DbConnection $dbConnection
    )
    {
    }

    public function findAllByCategoryAndPrice(?string $category, ?int $priceLessThan, int $limit = self::DEFAULT_LIMIT): array
    {
        $qb = $this->dbConnection->readQueryBuilder();
        $qb->select('*')
            ->from('products', 'p');

        if ($category) {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        if ($priceLessThan > 0) {
            $qb->andWhere('p.price_min_unit < :priceLessThan')
                ->setParameter('priceLessThan', $priceLessThan);
        }

        $result = $qb->setMaxResults($limit)->executeQuery()->fetchAllAssociative();
        return $this->mapRowsToProductArray($result);
    }

    private function mapRowsToProductArray(array $products): array
    {
        return array_map([$this, 'mapRowToProduct'], $products);
    }

    private function mapRowToProduct(array $product): Product
    {
        return Product::createFromArray($product);
    }

    public function findBySku(string $sku): ?Product
    {
        return $this->dbConnection->findOnyBy(Product::class, ['sku' => $sku]);
    }
}