<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241110094313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE products 
        (
            id INT AUTO_INCREMENT NOT NULL, 
            sku VARCHAR(100) NOT NULL, 
            name VARCHAR(100) NOT NULL, 
            category VARCHAR(50) NOT NULL, 
            price_min_unit INT NOT NULL, 
            currency VARCHAR(3) NOT NULL, 
            PRIMARY KEY(id),
            UNIQUE INDEX idx_sku (sku),
            INDEX idx_original_price_min_unit (price_min_unit),
            INDEX idx_category (category)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE products');
    }
}
