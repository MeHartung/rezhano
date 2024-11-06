<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190118070053 extends AbstractMigration
{
  public function up (Schema $schema)
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE moysklad_queue DROP FOREIGN KEY FK_A9D371F18D9F6D38');
    $this->addSql('ALTER TABLE moysklad_queue ADD CONSTRAINT FK_A9D371F18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE notification_order DROP FOREIGN KEY FK_339D76618D9F6D38');
    $this->addSql('ALTER TABLE notification_order ADD CONSTRAINT FK_339D76618D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
  }

  public function down (Schema $schema)
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE moysklad_queue DROP FOREIGN KEY FK_A9D371F18D9F6D38');
    $this->addSql('ALTER TABLE moysklad_queue ADD CONSTRAINT FK_A9D371F18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
    $this->addSql('ALTER TABLE notification_order DROP FOREIGN KEY FK_339D76618D9F6D38');
    $this->addSql('ALTER TABLE notification_order ADD CONSTRAINT FK_339D76618D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
  }
}
