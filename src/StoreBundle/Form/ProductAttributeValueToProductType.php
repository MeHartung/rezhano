<?php

namespace StoreBundle\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\ProductAttribute;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\ProductAttributeValue;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\Type\ProductType;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/*
 * Форма, генерирующая несколько полей
 * На каждый ProductAttribute, прикрепленный к переданному ProductType
 *   генерируется multiple поле, с возможностью добавлять новые, не существующие значения
 */
class ProductAttributeValueToProductType extends AbstractType
{
  private $em;
  private $newValues;
  protected $attributeValuesByTypes;

  public function configureOptions (OptionsResolver $resolver)
  {
    $resolver->setRequired('productType');
  }

  public function __construct (EntityManagerInterface $em)
  {
    $this->em = $em;
    $this->attributeValuesByTypes = [];
  }

  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    $productType = $options['productType'];

    if (!$productType instanceof ProductType)
    {
      throw new InvalidOptionsException();
    }

    $productTypeProductAttributes = $productType->getProductAttributes();
    $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'preSetData']);
    $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'preSubmitData']);
    $builder->addEventListener(FormEvents::SUBMIT, [$this, 'submitData']);

    /** @var ProductAttribute $attr */
    foreach ($productTypeProductAttributes as $attr)
    {
      $this->addAttributeField($builder, $attr);
    }
  }

  /**
   * Новые(не существующие) значения выбираем из формы, чтобы избужать ошибки валидации
   * @param FormEvent $event
   */
  public function preSubmitData(FormEvent $event)
  {
    $data = $event->getData();
    $newData = [];
    $newValues = [];

    if ($data)
    {
      foreach ($data as $typeAttributeId => $attributeValues)
      {
        $values = $this->getAttributeValuesByType($typeAttributeId);
        $newData[$typeAttributeId] = [];
        $newValues[$typeAttributeId] = [];

        foreach ($attributeValues as $attributeValue)
        {
          $existingValue = false;

          foreach ($values as $value)
          {
            if ($value->getValue() === $attributeValue)
            {
              $newData[$typeAttributeId][] = $attributeValue;
              $existingValue = true;
              continue 2;
            }
          }

          if (!$existingValue)
          {
            $newValues[$typeAttributeId][] = $attributeValue;
          }
        }
      }
    }

    $event->setData($newData);
    $this->newValues = $newValues;
  }

  /**
   * Переводим массив атрибутов в массив из их названий
   * @param FormEvent $event
   */
  public function preSetData(FormEvent $event)
  {
    $data = [];
    $parent = $event->getForm()->getParent();
    /** @var Product $product */
    $product = $parent->getData();

    if ($product && $product instanceof Product && !$event->getForm()->getData())
    {
      /** @var ProductType $productType */
      $productType = $event->getForm()->getConfig()->getOption('productType');
      $productValues = $product->getProductAttributeValues();

      foreach ($productType->getProductAttributes() as $productAttribute)
      {
        $data[$productAttribute->getId()] = [];
      }

      foreach ($productValues as $productValue)
      {
        $attr = $productValue->getProductAttribute();

        if (isset($data[$attr->getId()]))
        {
          $data[$attr->getId()][] = $productValue->getValue();
        }
      }

      $event->setData($data);
    }
  }

  /**
   * Переводим текстовые значения атрибутов в их entity
   * @param FormEvent $event
   */
  public function submitData(FormEvent $event)
  {
    $data = $event->getData();
    $parent = $event->getForm()->getParent();
    /** @var Product $product */
    $product = $parent->getData();
    $valuesCollection = new ArrayCollection();

    foreach ($data as $typeId => $attributes)
    {
      if (isset($this->newValues[$typeId]))
      {
        $attributes = array_merge($attributes, $this->newValues[$typeId]);
      }

      if (count($attributes))
      {
        /** @var ProductAttributeValue $attributeValue */
        foreach ($attributes as $attributeValue)
        {
          $attributeValue = $this->getOrCreateAttributeValueByName($typeId, $attributeValue);
          $valuesCollection->add($attributeValue);
        }
      }
    }

    $event->setData($valuesCollection);
  }

  /*
   * К этому имени привязан нужный виджет
   */
  public function getBlockPrefix ()
  {
    return 'product_attribute_value';
  }

  /**
   * Генерируем поле формы
   * @param FormBuilderInterface $builder
   * @param ProductAttribute $productAttribute
   */
  protected function addAttributeField(FormBuilderInterface $builder, ProductAttribute $productAttribute)
  {
    $choices = [];
    /** @var ProductAttributeValue[]|ArrayCollection $values */
    $values = $this->em
      ->getRepository('StoreBundle:Store\Catalog\Product\Attributes\ProductAttributeValue')
      ->getProductTypeAttributeValues($productAttribute->getId())
      ->getQuery()
      ->getResult();

    foreach ($values as $value)
    {
      $choices[$value->getValue()] = $value->getValue();
    }

    $builder
      ->add($productAttribute->getId(), 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
        'choices' => $choices,
        'label' => $productAttribute->getName(),
        'multiple' => true,
      ]);
  }

  /**
   * @param $productAttributeId
   * @return ProductAttributeValue[]|ArrayCollection
   */
  protected function getAttributeValuesByType($productAttributeId)
  {
    if (!isset($this->attributeValuesByTypes[$productAttributeId]))
    {
      $this->attributeValuesByTypes[$productAttributeId] = $this->em
        ->getRepository('StoreBundle:Store\Catalog\Product\Attributes\ProductAttributeValue')
        ->getProductTypeAttributeValues($productAttributeId)
        ->getQuery()
        ->getResult();
    }

    return $this->attributeValuesByTypes[$productAttributeId];
  }

  /**
   * @param $productAttributeId
   * @param $value
   * @return ProductAttributeValue
   */
  private function getOrCreateAttributeValueByName($productAttributeId, $value)
  {
    $values = $this->getAttributeValuesByType($productAttributeId);

    foreach ($values as $val)
    {
      if ($val->getValue() == $value)
      {
        return $val;
      }
    }

    $val = new ProductAttributeValue();
    $val->setValue($value);
    $val->setProductAttribute($this->getProductAttribute($productAttributeId));
    return $val;
  }

  /**
   * @param $productAttributeId
   * @return ProductAttribute
   */
  private function getProductAttribute($productAttributeId)
  {
    return $this->em->getRepository('StoreBundle:Store\Catalog\Product\Attributes\ProductAttribute')->find($productAttributeId);
  }
}