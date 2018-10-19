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

namespace  StoreBundle\Twig;


use StoreBundle\Util\EndingFormatter;

class EndingFormatTwigExtension extends \Twig_Extension
{
  public function getName()
  {
    return 'ending_format';
  }

  public function getFilters()
  {
    return array(
      new \Twig_SimpleFilter('ending_format', array($this, 'formatEnding'))
    );
  }

  public function formatEnding($number, $variants)
  {
    return EndingFormatter::format($number, $variants);
  }
}