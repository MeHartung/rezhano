<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 16.05.2018
 * Time: 18:36
 */

namespace Tests\DataFixtures\Taxon;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;

class LastChildTestTaxonFixtures extends Fixture
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
        $rootSubTaxonFirstChild1 = new Taxon();
        $rootSubTaxonFirstChild1->setName('Ребенок 1 ребёнка')
            ->setSlug('rst-1')
            ->setDescription('<h2>Видеорегистраторы</h2>')
            ->setShortName('Ребенок 1 ребёнка')
            ->setParent($rootSubTaxonFirst);
        $rootSubTaxonFirstChild2 = new Taxon();
        $rootSubTaxonFirstChild2->setName('Ребенок 1 ребёнка')
            ->setSlug('rst-1')
            ->setDescription('<h2>Видеорегистраторы</h2>')
            ->setShortName('Ребенок 1 ребёнка')
            ->setParent($rootSubTaxonFirst);
        $rootSubTaxonFirstChild3 = new Taxon();
        $rootSubTaxonFirstChild3->setName('Ребенок 1 ребёнка')
            ->setSlug('rst-1')
            ->setDescription('<h2>Видеорегистраторы</h2>')
            ->setShortName('Ребенок 1 ребёнка')
            ->setParent($rootSubTaxonFirst);
        $rootSubTaxonFirstChild4= new Taxon();
        $rootSubTaxonFirstChild4->setName('Ребенок 1 ребёнка')
            ->setSlug('rst-1')
            ->setDescription('<h2>Видеорегистраторы</h2>')
            ->setShortName('Ребенок 1 ребёнка')
            ->setParent($rootSubTaxonFirst);
        $rootSubTaxonFirstChild5 = new Taxon();
        $rootSubTaxonFirstChild5->setName('Ребенок 1 ребёнка')
            ->setSlug('rst-1')
            ->setDescription('<h2>Видеорегистраторы</h2>')
            ->setShortName('Ребенок 1 ребёнка')
            ->setParent($rootSubTaxonFirst);
        $rootSubTaxonFirstChild6 = new Taxon();
        $rootSubTaxonFirstChild6->setName('Ребенок 1 ребёнка')
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
        $manager->persist($rootSubTaxonFirstChild1);
        $manager->persist($rootSubTaxonFirstChild2);
        $manager->persist($rootSubTaxonFirstChild3);
        $manager->persist($rootSubTaxonFirstChild4);
        $manager->persist($rootSubTaxonFirstChild5);
        $manager->persist($rootSubTaxonFirstChild6);
        $manager->persist($rootSubTaxonSecondChild);
        $manager->flush();

        $this->addReference('six-child-taxon', $rootSubTaxonFirst);
        $this->addReference('one-chile-taxon', $rootSubTaxonSecond);
        $this->addReference("last-child-test", $rootSubTaxonSecondChild);
    }


}