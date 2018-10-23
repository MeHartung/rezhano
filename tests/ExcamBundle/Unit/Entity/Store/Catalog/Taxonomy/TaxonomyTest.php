<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 11.05.2018
 * Time: 18:51
 */

namespace Tests\StoreBundle\Unit\Entity\Store\Catalog\Taxonomy;


use StoreBundle\DataFixtures\Taxon\TaxonSortTestFixtures;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use Tests\StoreBundle\StoreWebTestCase;

class TaxonomyTest extends StoreWebTestCase
{
    /**
     * https://jira.accurateweb.ru/browse/EXCAM-176
     */
    public function testSortCatalogSectionsOnMainPage()
    {
        # Загрузим фикстуры
        $taxonRepo = $this->getEntityManager()->getRepository(Taxon::class);
        /** @FIXME почему-то фикстура льётся не очищая старые данные */
        foreach ($taxonRepo->findAll(["treeLeft"=>"DESC"]) as $taxon)
        {
            $this->em->remove($taxon);
            $this->em->flush();
        }
        $this->appendFixture(new TaxonSortTestFixtures(), true);

        $taxons = $taxonRepo->findTopMost();

        # проверим что записи в дефолтном порядке
        $this->assertEquals("first", $taxons[0]->getName(), "Сортировка разделов каталога не работает");
        $this->assertEquals("second", $taxons[1]->getName(), "Сортировка разделов каталога не работает");
        $this->assertEquals("third", $taxons[2]->getName(), "Сортировка разделов каталога не работает");

        # третий становится вторым
        $taxonRepo->moveUp($this->getByReference("taxon-sort-third"));

        $this->em->clear();
        $taxons = $taxonRepo->findTopMost();

        $this->assertEquals("first", $taxons[0]->getName(), "Сортировка разделов каталога не работает");
        $this->assertEquals("third", $taxons[1]->getName(), "Сортировка разделов каталога не работает");
        $this->assertEquals("second", $taxons[2]->getName(), "Сортировка разделов каталога не работает");

        # второй становится первым
        $taxonRepo->moveUp($this->getByReference("taxon-sort-second"), 2);

        $this->em->clear();
        $taxons = $taxonRepo->findTopMost();

        $this->assertEquals("second", $taxons[0]->getName(), "Сортировка разделов каталога не работает");
        $this->assertEquals("first", $taxons[1]->getName(), "Сортировка разделов каталога не работает");
        $this->assertEquals("third", $taxons[2]->getName(), "Сортировка разделов каталога не работает");
    }



}