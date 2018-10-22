<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181022105028 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
      $this->addSql('INSERT INTO `catalog_sections` (`id`, `tree_root`, `slug`, `name`, `description`, `tree_left`, `tree_level`, `tree_right`, `short_name`, `presentation_id`, `nb_products`) VALUES (NULL, \'1\', \'catalog\', \'Каталог\', \'<p>Корневой раздел</p>\', \'1\', \'0\', \'2\', \'Каталог\', \'1\', \'0\')');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
