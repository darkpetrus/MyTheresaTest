<?php

namespace App\Tests\e2e\Backoffice\Products\Infrastructure\Controller;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerIntegrationTest extends WebTestCase
{
    #[Test]
    public function endpoint_should_works(): void
    {
        $client = static::createClient();
        $client->request('GET', '/products');

        $this->assertResponseIsSuccessful();

        $this->assertJson($client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($data);
        $this->assertCount(5, $data);
    }

    #[Test]
    public function test_response_schema(): void
    {
        $client = static::createClient();
        $client->request('GET', '/products?category=boots');
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($data);

        $product = $data[0];

        if (!empty($product)) {
            $this->assertArrayHasKey('sku', $product);
            $this->assertArrayHasKey('name', $product);
            $this->assertArrayHasKey('category', $product);
            $this->assertArrayHasKey('price', $product);

            $this->assertArrayHasKey('original', $product['price']);
            $this->assertArrayHasKey('final', $product['price']);
            $this->assertArrayHasKey('discount_percentage', $product['price']);
            $this->assertArrayHasKey('currency', $product['price']);

            $this->assertEquals('EUR', $product['price']['currency']);
            $this->assertIsInt($product['price']['original']);
            $this->assertIsInt($product['price']['final']);
            $this->assertEquals('30%', $product['price']['discount_percentage']);

            $this->assertGreaterThan(0, $product['price']['original']);
            $this->assertGreaterThan(0, $product['price']['final']);
        }
    }
}