<?php

namespace App\Tests\unit\Backoffice\Products\Infrastructure\Command;

use App\Backoffice\Products\Domain\Model\Product;
use App\Backoffice\Products\Domain\Repository\ProductRepositoryInterface;
use App\Backoffice\Products\Domain\Service\CreateNewProductService;
use App\Backoffice\Products\Infrastructure\Command\ProductLoadCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ProductLoadCommandTest extends TestCase
{
    private MockObject|ProductRepositoryInterface $productRepository;
    private MockObject|CreateNewProductService $createNewProductService;
    private MockObject|KernelInterface $kernel;
    private MockObject|LoggerInterface $logger;

    private ProductLoadCommand $command;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->createNewProductService = $this->createMock(CreateNewProductService::class);
        $this->kernel = $this->createMock(KernelInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->command = new ProductLoadCommand(
            $this->productRepository,
            $this->createNewProductService,
            $this->kernel,
            $this->logger
        );
    }

    #[Test]
    public function test_execute_with_missing_file(): void
    {
        $this->kernel->method('getProjectDir')->willReturn('/nonexistent/path');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('The file products.json does not exist in the dataset folder.');

        $result = $this->command->run($input, $output);

        $this->assertEquals(Command::FAILURE, $result);
    }

    #[Test]
    public function test_execute_with_invalid_json(): void
    {
        $projectDir = __DIR__;
        $this->kernel->method('getProjectDir')->willReturn($projectDir);

        $datasetDir = $projectDir . '/dataset';
        mkdir($datasetDir);
        file_put_contents($projectDir . '/dataset/products.json', '{ invalid json }');

        $this->logger->expects($this->once())
            ->method('error')
            ->with('Failed to decode the JSON file.');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $command = new ProductLoadCommand($this->productRepository, $this->createNewProductService, $this->kernel, $this->logger);
        $result = $command->run($input, $output);

        unlink($projectDir . '/dataset/products.json');
        rmdir($datasetDir);

        $this->assertEquals(Command::FAILURE, $result);
    }

    #[Test]
    public function should_execute_successfully(): void
    {
        $projectDir = __DIR__;
        $this->kernel->method('getProjectDir')->willReturn($projectDir);

        $productData = [
            'products' => [
                ['sku' => '001', 'name' => 'Test Product', 'price' => 100, 'category' => 'Category1'],
                ['sku' => '002', 'name' => 'Another Product', 'price' => 150, 'category' => 'Category2'],
            ]
        ];
        $datasetDir = $projectDir . '/dataset';
        mkdir($datasetDir);
        file_put_contents($projectDir . '/dataset/products.json', json_encode($productData));

        $this->createNewProductService->expects($this->exactly(2))->method('execute')->willReturnCallback(
            function ($sku, $name, $price, $category) {
                $this->assertContains($sku, ['001', '002']);
                $this->assertContains($name, ['Test Product', 'Another Product']);
                $this->assertContains($price, [100, 150]);
                $this->assertContains($category, ['Category1', 'Category2']);
                return $this->createMock(Product::class);
            }
        );

        $this->productRepository->expects($this->exactly(2))->method('save');

        $this->logger->expects($this->once())
            ->method('info')
            ->with('Products have been successfully loaded and saved to the database.');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $command = new ProductLoadCommand($this->productRepository, $this->createNewProductService, $this->kernel, $this->logger);
        $result = $command->run($input, $output);

        unlink($projectDir . '/dataset/products.json');
        rmdir($datasetDir);

        $this->assertEquals(Command::SUCCESS, $result);
    }

    #[Test]
    public function test_execute_with_failed_product_insertion_logs_error(): void
    {
        $projectDir = __DIR__;
        $this->kernel->method('getProjectDir')->willReturn($projectDir);

        $productData = [
            'products' => [
                ['sku' => '001', 'name' => 'Test Product', 'price' => 100, 'category' => 'Category1'],
                ['sku' => '002', 'name' => 'Another Product', 'price' => 150, 'category' => 'Category2'],
            ]
        ];

        $datasetDir = $projectDir . '/dataset';
        mkdir($datasetDir);
        file_put_contents($projectDir . '/dataset/products.json', json_encode($productData));

        // Simula un fallo en la creación del segundo producto
        $this->createNewProductService->expects($this->exactly(2))
            ->method('execute')
            ->willReturnOnConsecutiveCalls(
                $this->createMock(Product::class),
                $this->throwException(new \Exception('Database error'))
            );

        $this->productRepository->expects($this->once())->method('save');

        $this->logger->expects($this->exactly(1))
            ->method('error')
            ->withConsecutive(
                ['Failed to insert product', $productData['products'][1]]
            );

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $command = new ProductLoadCommand($this->productRepository, $this->createNewProductService, $this->kernel, $this->logger);
        $result = $command->run($input, $output);

        unlink($projectDir . '/dataset/products.json');
        rmdir($datasetDir);

        $this->assertEquals(Command::SUCCESS, $result);
    }


}