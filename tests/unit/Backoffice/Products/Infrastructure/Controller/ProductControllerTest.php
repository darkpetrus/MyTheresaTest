<?php

namespace App\Tests\unit\Backoffice\Products\Infrastructure\Controller;

use App\Backoffice\Products\Application\UseCase\GetProductsByCategoryUseCase;
use App\Backoffice\Products\Application\UseCase\GetProductsBySkuUseCase;
use App\Backoffice\Products\Infrastructure\Controller\ProductController;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends TestCase
{

    private MockObject|GetProductsByCategoryUseCase $getProductsUseCaseMock;
    private MockObject|GetProductsBySkuUseCase $getProductsBySkuUseCaseMock;

    private ProductController $controller;

    protected function setUp(): void
    {
        $this->getProductsUseCaseMock = $this->createMock(GetProductsByCategoryUseCase::class);
        $this->getProductsBySkuUseCaseMock = $this->createMock(GetProductsBySkuUseCase::class);
        $this->controller = new ProductController(
            $this->getProductsUseCaseMock,
            $this->getProductsBySkuUseCaseMock
        );
    }

    #[Test]
    public function should_get_products_success(): void
    {
        $this->getProductsUseCaseMock
            ->method('execute')
            ->willReturn(['product1', 'product2']);

        $request = Request::create('/products');
        $response = $this->controller->getProducts($request);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(['product1', 'product2'], $data);
    }

    #[Test]
    public function should_throw_error(): void
    {
        $this->getProductsUseCaseMock
            ->method('execute')
            ->willThrowException(new \Exception('Some error occurred'));

        $request = Request::create(
            '/products', 'GET',
            [
                'category' => 'a-category',
            ]
        );
        $response = $this->controller->getProducts($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertContains('Some error occurred', $data);
    }

}