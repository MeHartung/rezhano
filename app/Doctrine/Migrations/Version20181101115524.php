<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181101115524 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE5F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES shipping_method (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE5F7D6850 ON orders (shipping_method_id)');
        $this->addSql('ALTER TABLE shipping_method ADD cost INT DEFAULT NULL, ADD free_delivery_threshold INT DEFAULT NULL, DROP cost_info');
        $this->addSql('ALTER TABLE cheese_stories DROP title');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cheese_stories ADD title VARCHAR(256) DEFAULT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE5F7D6850');
        $this->addSql('DROP INDEX IDX_E52FFDEE5F7D6850 ON orders');
        $this->addSql('ALTER TABLE shipping_method ADD cost_info VARCHAR(512) DEFAULT NULL COLLATE utf8_unicode_ci, DROP cost, DROP free_delivery_threshold');
    }
}
