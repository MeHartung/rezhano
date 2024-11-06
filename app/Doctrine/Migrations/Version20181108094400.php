<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181108094400 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_attributes ADD show_in_filter TINYINT(1) DEFAULT \'1\' NOT NULL, ADD show_in_product TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE product_attribute_value CHANGE value value LONGTEXT NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_attribute_value CHANGE value value VARCHAR(1024) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE product_attributes DROP show_in_filter, DROP show_in_product');
    }
}
