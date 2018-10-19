<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 18.05.2018
 * Time: 9:34
 */

namespace Tests\DataFixtures\Taxon;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;

class TaxonMoveNodeTestFixture extends Fixture
{
    public function load (ObjectManager $manager)
    {
        $taxon = new Taxon();
        $taxon->setName('Каталог')
            ->setSlug('katalogh')
            ->setParent(null)
            ->setDescription(null)
            ->setShortName('Каталог');

        $smartphoneTaxon =  new Taxon();
        $smartphoneTaxon->setName('Смартфоны')
            ->setSlug('smart')
            ->setParent($taxon)
            ->setDescription(null)
            ->setShortName('смарты');

            $smartphoneHTC =  new Taxon();
            $smartphoneHTC->setName('HTC')
                ->setSlug('smart')
                ->setParent($smartphoneTaxon)
                ->setDescription(null)
                ->setShortName('смарты');

            $smartphoneMi=  new Taxon();
            $smartphoneMi->setName('mi')
                ->setSlug('smart')
                ->setParent($smartphoneTaxon)
                ->setDescription(null)
                ->setShortName('смарты');

                $smartphoneMiNote=  new Taxon();
                $smartphoneMiNote->setName('minote')
                    ->setSlug('smart')
                    ->setParent($smartphoneMi)
                    ->setDescription(null)
                    ->setShortName('смарты');
                $smartphoneMiPro=  new Taxon();
                $smartphoneMiPro->setName('mipro')
                    ->setSlug('smart')
                    ->setParent($smartphoneMi)
                    ->setDescription(null)
                    ->setShortName('смарты');
                $smartphoneMiMi=  new Taxon();
                $smartphoneMiMi->setName('mimi')
                    ->setSlug('smart')
                    ->setParent($smartphoneMi)
                    ->setDescription(null)
                    ->setShortName('смарты');

            $smartphoneMeizu =  new Taxon();
            $smartphoneMeizu->setName('meizu')
                ->setSlug('smart')
                ->setParent($smartphoneTaxon)
                ->setDescription(null)
                ->setShortName('смарты');

            $smartphoneLg =  new Taxon();
            $smartphoneLg->setName('lg')
                ->setSlug('smart')
                ->setParent($smartphoneTaxon)
                ->setDescription(null)
                ->setShortName('смарты');

        $tvTaxon =  new Taxon();
        $tvTaxon->setName('TV')
            ->setSlug('tv')
            ->setParent($taxon)
            ->setDescription(null)
            ->setShortName('tv');

            $tvTaxonLg =  new Taxon();
            $tvTaxonLg->setName('LG-TV')
                ->setSlug('tv')
                ->setParent($tvTaxon)
                ->setDescription(null)
                ->setShortName('tv');
            $tvTaxonSamsung =  new Taxon();
            $tvTaxonSamsung->setName('Samsung')
                ->setSlug('tv')
                ->setParent($tvTaxon)
                ->setDescription(null)
                ->setShortName('tv');

        $bagTaxon =  new Taxon();
        $bagTaxon->setName('Сумки')
            ->setSlug('bags')
            ->setParent($taxon)
            ->setDescription(null)
            ->setShortName('tv');

            $bagTaxonNotebook =  new Taxon();
            $bagTaxonNotebook->setName('asd')
                ->setSlug('tv')
                ->setParent($bagTaxon)
                ->setDescription(null)
                ->setShortName('tv');
            $bagTaxonMonitor =  new Taxon();
            $bagTaxonMonitor
                ->setName("monitor bag")
                ->setSlug('ad')
                ->setParent($bagTaxon)
                ->setDescription(null)
                ->setShortName('tv');

        $etcTaxon =  new Taxon();
        $etcTaxon->setName('Прочее')
            ->setSlug('etc')
            ->setParent($taxon)
            ->setDescription(null)
            ->setShortName('tv');

        $manager->persist($taxon);
            $manager->persist($smartphoneTaxon);
                $manager->persist($smartphoneHTC);
                $manager->persist($smartphoneMi);
                    $manager->persist($smartphoneMiNote);
                    $manager->persist($smartphoneMiPro);
                    $manager->persist($smartphoneMiMi);
                $manager->persist($smartphoneMeizu);
                $manager->persist($smartphoneLg);
            $manager->persist($tvTaxon);
                $manager->persist($tvTaxonSamsung);
                $manager->persist($tvTaxonLg);
            $manager->persist($bagTaxon);
                $manager->persist($bagTaxonNotebook);
                $manager->persist($bagTaxonMonitor);
            $manager->persist($etcTaxon);

        $manager->flush();

        $this->addReference("tm-smartphone", $smartphoneTaxon);
            $this->addReference("tm-smartphone-mi", $smartphoneMi);
        $this->addReference("tm-tv", $tvTaxon);
        $this->addReference("tm-bag", $bagTaxon);
            $this->addReference("tm-bag-monitor", $bagTaxonMonitor);
        $this->addReference("tm-etc", $etcTaxon);
    }


}