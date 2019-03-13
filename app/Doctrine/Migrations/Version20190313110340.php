<?php declare(strict_types = 1);

namespace Application\Migrations;

use Accurateweb\SlugifierBundle\Model\YandexSlugifier;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190313110340 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cheese_stories ADD slug VARCHAR(255) NOT NULL');
    }

    public function postUp(Schema $schema)
    {
      parent::postUp($schema);
      # это должн быть сделано до того, как добавиться индекс на slug
      $this->addSlugs();
      $this->removeDuplicate();
  
      $this->addSql('CREATE UNIQUE INDEX UNIQ_62EEA25F989D9B62 ON cheese_stories (slug)');
    }
  
  public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
  
        $this->addSql('DROP INDEX UNIQ_62EEA25F989D9B62 ON cheese_stories');
        $this->addSql('ALTER TABLE cheese_stories DROP slug');
      
    }
    
    private function addSlugs()
    {
      $slugifier = new YandexSlugifier();
  
      # сначала просто запишем все алиасы
      foreach ($this->connection->fetchAll('SELECT * FROM cheese_stories') as $story)
      {
        $slug = $slugifier->slugify($story['title']);
        $sql= 'UPDATE cheese_stories SET slug = :slug WHERE id = :id';
    
        $update = $this->connection->prepare($sql);
    
        $update->bindValue(':slug', $slug);
        $update->bindValue(':id', $story['id']);
        $update->execute();
      }
    }
  
    
    private function removeDuplicate()
    {
      $query = $this->connection->prepare('SELECT `id`, `slug` FROM articles WHERE `slug` IN (SELECT `slug` FROM `articles` GROUP BY `slug` HAVING COUNT(`slug`)>"1" )');
      $query->execute();
  
      $i = 0;
  
      while ($row = $query->fetch(\PDO::FETCH_ASSOC))
      {
        $sql='UPDATE cheese_stories SET slug = :slug WHERE id = :id';
    
        $update = $this->connection->prepare($sql);
    
        $update->bindValue(':slug', $row['slug'] . "-" . $i);
        $update->bindValue(':id', $row['id']);
        $update->execute();
        $i++;
      }
    }
}
