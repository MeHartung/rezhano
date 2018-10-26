<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181024065944 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE catalog_sections DROP FOREIGN KEY FK_19419D8689D223D5');
        $this->addSql('DROP TABLE alfabank_credit_taxons');
        $this->addSql('DROP INDEX IDX_19419D8689D223D5 ON catalog_sections');
        $this->addSql('ALTER TABLE catalog_sections DROP alfa_bank_taxon_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE alfabank_credit_taxons (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_general_ci, description VARCHAR(255) NOT NULL COLLATE utf8_general_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE catalog_sections ADD alfa_bank_taxon_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE catalog_sections ADD CONSTRAINT FK_19419D8689D223D5 FOREIGN KEY (alfa_bank_taxon_id) REFERENCES alfabank_credit_taxons (id)');
        $this->addSql('CREATE INDEX IDX_19419D8689D223D5 ON catalog_sections (alfa_bank_taxon_id)');
    }
}
