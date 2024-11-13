<?php

namespace App\Backoffice\Products\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "products")]
class Product
{
    private const DEFAULT_CURRENCY = 'EUR';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $sku;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50)]
    private string $category;

    #[ORM\Column(type: 'integer')]
    private int $priceMinUnit;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    /**
     * @param int|null $id
     * @param string $sku
     * @param string $name
     * @param string $category
     * @param int $priceMinUnit
     * @param string $currency
     */
    public function __construct(
        ?int $id, string $sku, string $name, string $category, int $priceMinUnit, string $currency)
    {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->category = $category;
        $this->priceMinUnit = $priceMinUnit;
        $this->currency = $currency;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function priceMinUnit(): int
    {
        return $this->priceMinUnit;
    }

    public function sku(): string
    {
        return $this->sku;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public static function createFromArray(array $data): self
    {
        if(!isset($data['price_min_unit']) && isset($data['price'])){
            $data['price_min_unit'] = $data['price'];
        }
        return new self(
            $data['id'] ?? null,
            $data['sku'],
            $data['name'],
            $data['category'],
            $data['price_min_unit'],
            $data['currency'] ?? self::DEFAULT_CURRENCY,
        );
    }
}