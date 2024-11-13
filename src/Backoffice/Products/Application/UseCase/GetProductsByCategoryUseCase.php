<?php

namespace App\Backoffice\Products\Application\UseCase;


use App\Backoffice\Products\Domain\Repository\ProductSearchRepositoryInterface;
use App\Backoffice\Products\Domain\Serializer\ProductListSerializer;
use Symfony\Contracts\Cache\CacheInterface;

class GetProductsByCategoryUseCase
{
    private const DEFAULT_CACHE_KEY = 'allProductsKey';

    public function __construct(
        private readonly ProductSearchRepositoryInterface $repository,
        private readonly ProductListSerializer            $productListSerializer,
        private readonly CacheInterface                   $cache
    ){
    }

    function execute(?string $category = null, ?int $priceLessThan = null): array
    {
        $cacheKey = $category.$priceLessThan;

        if(empty($cacheKey)){
            $cacheKey = self::DEFAULT_CACHE_KEY;
        }

        $cachedProducts = $this->cache->getItem($cacheKey);
        // In order to provide product list as fast as possible to all customers
        // We use this generic key to provide same result for same request
        // Note: You can refresh cache when new products are created.
        if(!$cachedProducts->isHit() || empty($cachedProducts->get())){
            $products = $this->repository->findAllByCategoryAndPrice($category, $priceLessThan);
            $serializedResponse = $this->productListSerializer->serialize($products);

            $cachedProducts->set($serializedResponse);
            $cachedProducts->expiresAfter(3600);
            $this->cache->save($cachedProducts);
        }else{
            $serializedResponse = $cachedProducts->get();
        }

        return $serializedResponse;
    }
}