<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 11.05.2018
 * Time: 18:53
 */

namespace Tests\DataFixtures\Taxon;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;

class TaxonSortTestFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $taxon = new Taxon();
        $taxon->setName('Каталог')
            ->setSlug('katalogh')
            ->setParent(null)
            ->setDescription(null)
            ->setShortName('Каталог');

        $firstTaxon = new Taxon();
        $firstTaxon->setName('first')
            ->setSlug('first')
            ->setDescription('first')
            ->setShortName('short first')
            ->setParent($taxon);

        $secondTaxon = new Taxon();
        $secondTaxon->setName('second')
            ->setSlug('second')
            ->setDescription('second')
            ->setShortName('short second')
            ->setParent($taxon);

        $thirdTaxon = new Taxon();
        $thirdTaxon->setName('third')
            ->setSlug('third')
            ->setDescription('third')
            ->setShortName('short third')
            ->setParent($taxon);

        $manager->persist($firstTaxon);
        $manager->persist($secondTaxon);
        $manager->persist($thirdTaxon);

        $manager->flush();

        $this->setReference('taxon-sort-first', $firstTaxon);
        $this->setReference('taxon-sort-second', $secondTaxon);
        $this->setReference('taxon-sort-third', $thirdTaxon);
    }
}