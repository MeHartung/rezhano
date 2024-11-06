<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181026132608 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_product (product_source INT NOT NULL, product_target INT NOT NULL, INDEX IDX_2931F1D3DF63ED7 (product_source), INDEX IDX_2931F1D24136E58 (product_target), PRIMARY KEY(product_source, product_target)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_product ADD CONSTRAINT FK_2931F1D3DF63ED7 FOREIGN KEY (product_source) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_product ADD CONSTRAINT FK_2931F1D24136E58 FOREIGN KEY (product_target) REFERENCES products (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_product');
    }
}
