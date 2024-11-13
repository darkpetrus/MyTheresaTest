<?php

namespace App\Backoffice\Products\Infrastructure\Controller;

use App\Backoffice\Products\Application\UseCase\GetProductsByCategoryUseCase;
use App\Backoffice\Products\Application\UseCase\GetProductsBySkuUseCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly GetProductsByCategoryUseCase $getProductsByCategory,
        private readonly GetProductsBySkuUseCase $getProductsBySku
    )
    {
    }

    public function getProducts(Request $request): JsonResponse {

        $category = $request->query->get('category');
        $priceLessThan = $request->query->get('priceLessThan');

        try{
           $result = $this->getProductsByCategory->execute($category,$priceLessThan);
        }catch (\Throwable $e){
            return new JsonResponse([
                $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }

    public function getProductsBySku(Request $request): JsonResponse {

        $sku = $request->query->get('sku');
        try{
            $result = $this->getProductsBySku->execute($sku);
        }catch (\Throwable $e){
            return new JsonResponse([
                $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }

}