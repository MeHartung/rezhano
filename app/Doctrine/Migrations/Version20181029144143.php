<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181029144143 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //$this->addSql('CREATE TABLE moysklad_queue (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, sent_at DATETIME DEFAULT NULL, message LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_A9D371F18D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        //$this->addSql('ALTER TABLE moysklad_queue ADD CONSTRAINT FK_A9D371F18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        //$this->addSql('ALTER TABLE orders ADD moysklad_sent TINYINT(1) DEFAULT \'0\', CHANGE shipping_method_id shipping_method_id INT DEFAULT NULL');
        $this->addSql('CREATE TABLE IF NOT EXISTS shipping_method (uid VARCHAR(64) NOT NULL, name VARCHAR(128) NOT NULL, help LONGTEXT DEFAULT NULL, position INT NOT NULL, PRIMARY KEY(uid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shipping_method ADD id INT AUTO_INCREMENT NOT NULL, ADD cost_info VARCHAR(512) DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE5F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES shipping_method (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE5F7D6850 ON orders (shipping_method_id)');
        $this->addSql('ALTER TABLE payment_methods ADD info VARCHAR(512) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE moysklad_queue');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE5F7D6850');
        $this->addSql('DROP INDEX IDX_E52FFDEE5F7D6850 ON orders');
        $this->addSql('ALTER TABLE orders DROP moysklad_sent, CHANGE shipping_method_id shipping_method_id VARCHAR(36) DEFAULT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE payment_methods DROP info');
        $this->addSql('ALTER TABLE shipping_method MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE shipping_method DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE shipping_method DROP id, DROP cost_info');
    }
}
