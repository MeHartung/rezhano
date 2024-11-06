<?php
/**
 * (c) 2017 ИП Рагозин Денис Николаевич. Все права защищены.
 *
 * Настоящий файл является частью программного продукта, разработанного ИП Рагозиным Денисом Николаевичем
 * (ОГРНИП 315668300000095, ИНН 660902635476).
 *
 * Алгоритм и исходные коды программного кода программного продукта являются коммерческой тайной
 * ИП Рагозина Денис Николаевича. Любое их использование без согласия ИП Рагозина Денис Николаевича рассматривается,
 * как нарушение его авторских прав.
 *
 * Ответственность за нарушение авторских прав наступает в соответствии с действующим законодательством РФ.
 */

/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 25.10.2017
 * Time: 12:05
 */

namespace StoreBundle\Form\DataTransformer;

use Sonata\CoreBundle\Form\Type\BooleanType;
use Symfony\Component\Form\DataTransformerInterface;

class NullableBooleanToBooleanTransformer implements DataTransformerInterface
{
  /**
   * {@inheritdoc}
   */
  public function transform($value)
  {
    if (null === $value)
    {
      return '';
    }

    if ($value === true or (int) $value === BooleanType::TYPE_YES) {
      return BooleanType::TYPE_YES;
    }

    return BooleanType::TYPE_NO;
  }

  /**
   * {@inheritdoc}
   */
  public function reverseTransform($value)
  {
    if ('' === $value)
    {
      return null;
    }

    if ($value === BooleanType::TYPE_YES) {
      return true;
    }

    return false;
  }
}