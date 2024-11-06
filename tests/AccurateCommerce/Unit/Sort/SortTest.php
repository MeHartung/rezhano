<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 27.03.18
 * Time: 19:28
 */

namespace AccurateCommerce\Sort;


use AccurateCommerce\Model\Taxonomy\StaticTaxon;
use AccurateCommerce\Store\Catalog\Filter\ProductFilter;
use Tests\StoreBundle\StoreWebTestCase;

class SortTest extends StoreWebTestCase
{
  public function setUp()
  {
    parent::setUp();
  }

  /**
   * @link https://jira.accurateweb.ru/browse/EXCAM-196
   */
  public function testProductsNotPurshasableSort()
  {
    $result = $this->getFilterResult();
    /**
     * Применили фильтр и проверяем, неопуб. товар будет последним
     */
    $this->assertFalse($result[count($result)-1]->isPurchasable());

    /**
     * Сделаем первый товар снятым с продажи
     */
    $result[0]->setIsPurchasable(false);
    $this->em->persist($result[0]);
    $this->em->flush();
    $this->em->clear();

    $result = $this->getFilterResult();

    $this->assertFalse($result[count($result)-1]->isPurchasable());
    $this->assertFalse($result[count($result)-2]->isPurchasable());
    $this->assertNotFalse($result[0]->isPurchasable());
  }

  public function getFilterResult()
  {
    $taxonObj = $this->getByReference('taxon-sort');

    $taxon = new StaticTaxon($this->em->getRepository('StoreBundle:Store\Catalog\Product\Product'), $taxonObj);

    $productFilter = new ProductFilter(null, $taxon);
    $productQueryBuilder = $productFilter->apply();

    $sort = new ProductSort('price','order');
    $sort->apply($productQueryBuilder);

    return $result = $productQueryBuilder->getQuery()->getResult();
  }


}