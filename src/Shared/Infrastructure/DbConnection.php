<?php

namespace App\Shared\Infrastructure;

use App\Backoffice\Products\Domain\Model\Product;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class DbConnection
{
    public function __construct(
        private readonly Connection $connection,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function fetchOne(string $query, array $params = [], array $types = []): ?array
    {
        $value = $this->connection->executeQuery(
            $query,
            $params,
            $types
        )->fetchAssociative();

        return $value ?? null;
    }

    public function fetchAll(string $query, array $params = [], array $types = []): ?array
    {
        $value = $this->connection->executeQuery(
            $query,
            $params,
            $types
        )->fetchAllAssociative();

        return $value ?? null;
    }

    public function findOnyBy(string $className, array $criteria): ?Product
    {
        return $this->entityManager->getRepository($className)
            ->findOneBy($criteria);
    }


    public function readQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }
}