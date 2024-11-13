<?php

namespace App\Backoffice\Products\Domain\ValueObject;

enum ProductCategory: string
{
    case BOOTS = 'boots';
    case SANDALS = 'sandals';
    case SNEAKERS = 'sneakers';
    case UNKNOWN = 'unknown';


    public static function fromString(string $category): self
    {
        return match (strtolower($category)) {
            'boots' => self::BOOTS,
            'sandals' => self::SANDALS,
            'sneakers' => self::SNEAKERS,
            default => self::UNKNOWN,
        };
    }

    public static function isBootsFromString(string $category): bool
    {
        $productCategory = self::fromString($category);
        return $productCategory === self::BOOTS;
    }

    public function isBoots(): bool{
        return $this === self::BOOTS;
    }
}
