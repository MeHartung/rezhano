<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 18.08.17
 * Time: 11:13
 */

namespace StoreBundle\Form\ProductAttribute;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductAttributeType extends AbstractType
{

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $em = $this->getModelManager()
      ->getEntityManager('StoreBundle:Store\Catalog\Product\Product');

    $productTypeId = $this->getSubject()->getProduct()->getProductType(); //получаем Id типа

    //$productId = $this->getSubject()->getProduct()->getId(); //получаем ID текущего товара


    /**
     * Запрос для получения ТОЛЬКО атрибутов типа.
     * Т.к. product_attribute_id = id атрибута, то для запроса нам нужны только a.name (для label)
     * и a.id (для запроса)
     */

    $attributeQuery = $em->createQueryBuilder('d')
      ->select('a')
      ->from('StoreBundle:Store\Catalog\Product\Product', 'd')
      ->leftJoin('StoreBundle:Store\Catalog\Product\Attributes\Type\ProductType', 'o', 'WITH', 'o.id > 0')
      ->leftJoin('StoreBundle:Store\Catalog\Product\Attributes\Type\ProductTypeProductAttribute', 'p', 'WITH', 'p.productType = o.id')
      ->leftJoin('StoreBundle:Store\Catalog\Product\Attributes\ProductAttribute', 'a',  'WITH', 'a.id = p.productAttribute')
      ->where('d.productType = :id')
      ->setParameter('id', $productTypeId)
      ->andWhere('o.id = d.productType')
    ;

    $attributsArray = $attributeQuery->getQuery()->getResult();

    /**
     * Получаем массив, который будет исп. для заполнения шаблона запроса вывода полей с значениями атрибута.
     */
    foreach ($attributsArray as $key=>$attr)
    {
      $scamArray = [
        'id' => $attributsArray[$key]->getId(),
        'label' => $attributsArray[$key]->getName(),
      ];
      $sourceForQuery[] = $scamArray;
    }

    $countSourceForQuery = count($sourceForQuery);

    for ($i=0; $i<$countSourceForQuery; $i++)
    {
      $builder
        ->add('product')
        ->add($sourceForQuery[$i]['label'], 'sonata_type_model', [
          'query' => $em->createQueryBuilder('d')
            ->select('s') //'a'
            ->from('StoreBundle:Store\Catalog\Product\Product', 'd')
            ->leftJoin('StoreBundle:Store\Catalog\Product\Attributes\Type\ProductType', 'o', 'WITH', 'o.id > 1')
            ->leftJoin('StoreBundle:Store\Catalog\Product\Attributes\Type\ProductTypeProductAttribute', 'p', 'WITH', 'p.productType = o.id')
            ->leftJoin('StoreBundle:Store\Catalog\Product\Attributes\ProductAttribute', 'a',  'WITH', 'a.id = p.productAttribute')
            ->leftJoin('StoreBundle:Store\Catalog\Product\Attributes\ProductAttributeValue', 's',  'WITH', 's.productAttribute = a.id')
            ->where('d.productType = :id')
            ->setParameter('id', $productTypeId)
            ->andWhere('o.id = d.productType')
            ->andWhere('a.id = :idd')         //получаем значения именно этого атрибута
            ->setParameter('idd', $sourceForQuery[$i]['id']),
        ])
      ;
    }

  }

}