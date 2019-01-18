<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190118074123 extends AbstractMigration
{
  public function up (Schema $schema)
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY FK_471AD77E8D9F6D38');
    $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT FK_471AD77E8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
  }

  public function down (Schema $schema)
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY FK_471AD77E8D9F6D38');
    $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT FK_471AD77E8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
  }
}
