<?php


namespace Tests\DataFixtures\Taxon;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;


class InvalidTaxonFixtures extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $rootSubTaxonFirst =  new Taxon();
    $rootSubTaxonFirst->setName('Ребенок 1')
      ->setSlug('child-1')
      ->setDescription('<h2>Ребенок</h2>')
      ->setShortName('Ребенок 1')
      ->setParent($this->getReference('taxon-root'));

    $rootSubTaxonSecond =  new Taxon();
    $rootSubTaxonSecond->setName('Ребенок 2')
      ->setSlug('child-2')
      ->setDescription('<h2>Ребенок</h2>')
      ->setShortName('Ребенок 2')
      ->setParent($this->getReference('taxon-root'));

    /**
     * Дадим каждому ребёнку root каталога еще по ребёнку
     */
    $rootSubTaxonFirstChild = new Taxon();
    $rootSubTaxonFirstChild->setName('Ребенок 1 ребёнка')
      ->setSlug('rst-1')
      ->setDescription('<h2>Видеорегистраторы</h2>')
      ->setShortName('Ребенок 1 ребёнка')
      ->setParent($rootSubTaxonFirst);

    $rootSubTaxonSecondChild = new Taxon();
    $rootSubTaxonSecondChild->setName('Ребенок 2 ребёнка')
      ->setSlug('rst-2')
      ->setDescription('<h2>Видеорегистраторы</h2>')
      ->setShortName('Ребенок 2 ребёнка')
      ->setParent($rootSubTaxonSecond);

    $manager->persist($rootSubTaxonSecond);
    $manager->persist($rootSubTaxonFirst);
    $manager->persist($rootSubTaxonFirstChild);
    $manager->persist($rootSubTaxonSecondChild);
    $manager->flush();

    $this->addReference('first-invalid-2-lvl', $rootSubTaxonFirstChild);
    $this->addReference('second-invalid-2-lvl', $rootSubTaxonFirstChild);
    $this->addReference('first-invalid-3-lvl', $rootSubTaxonFirstChild);
    $this->addReference('second-invalid-3-lvl', $rootSubTaxonSecondChild);
  }
  
  
}