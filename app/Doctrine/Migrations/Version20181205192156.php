<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181205192156 extends AbstractMigration
{
  public function up(Schema $schema)
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE product_attributes ADD position INT NOT NULL, DROP weight');
    $this->addSql('set @i=-1;
                         UPDATE product_attributes SET position = (@i:=@i+1) WHERE id <> 0');
  }
  
  public function down(Schema $schema)
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE product_attributes ADD weight INT DEFAULT 0 NOT NULL, DROP position');
    
  }
}
