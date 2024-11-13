<?php

namespace App\Backoffice\Products\Infrastructure\Command;

use App\Backoffice\Products\Domain\Repository\ProductRepositoryInterface;
use App\Backoffice\Products\Domain\Service\CreateNewProductService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Throwable;

class ProductLoadCommand extends Command
{
    protected static string $defaultName = 'app:load-products';

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CreateNewProductService $createNewProductService,
        private readonly KernelInterface $kernel,
        private readonly LoggerInterface $logger
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('app:load-products');
        $this->setDescription('Loads products from a JSON file and saves them to the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectDir = $this->kernel->getProjectDir();
        $jsonFilePath = $projectDir . '/dataset/products.json';

        if (!file_exists($jsonFilePath)) {
            $this->logger->error('The file products.json does not exist in the dataset folder.');
            return Command::FAILURE;
        }

        $jsonData = file_get_contents($jsonFilePath);
        $productsArray = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('Failed to decode the JSON file.');
            return Command::FAILURE;
        }

        foreach ($productsArray['products'] as $productData) {
            try {
                $product = $this->createNewProductService->execute(
                    $productData['sku'],
                    $productData['name'],
                    $productData['price'],
                    $productData['category']
                );

                $this->productRepository->save($product);
            } catch (Throwable){
                continue;
            }
        }

        $this->logger->info('Products have been successfully loaded and saved to the database.');
        return Command::SUCCESS;
    }
}