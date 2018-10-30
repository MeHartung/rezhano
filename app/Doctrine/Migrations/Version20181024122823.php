<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181024122823 extends AbstractMigration
{
  public function up(Schema $schema)
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('INSERT INTO `menu` (`name`, `head`, `foot`, `tree_left`, `tree_level`, `tree_right`) VALUES (\'Меню\', \'0\', \'0\', \'1\', \'0\', \'2\');');
  }
  
  public function postUp(Schema $schema)
  {
    $this->addSql('UPDATE `menu` SET `parent_id`=LAST_INSERT_ID() WHERE `id`=LAST_INSERT_ID()');
  }
  
  public function down(Schema $schema)
  {
    // this down() migration is auto-generated, please modify it to your needs
    
  }
}
