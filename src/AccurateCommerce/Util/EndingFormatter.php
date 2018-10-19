<?php

namespace AccurateCommerce\Util;

/**
 * Описание класса EndingFormatter
 *
 * @author Dancy
 */
class EndingFormatter 
{
 /**
  * Форматирует окончание подписи суммы
  * Порядок падежей: И, Р, Р мн.ч (раздел, раздела, разделов)
  * 
  * @param int|float $count
  * @param array $variants раздел, раздела, разделов
  * @returns String
  */
  static public function format($count, $variants)
  {
    if (0 === $count)
    {
      return $variants[2];
    }
    
    if ($count != floor($count))
    {
      return $variants[1];
    }
    
    $md100 = $count % 100;
    $mod = $count % 10;
    
    if ($mod == 1 && $md100 != 11) 
      return $variants[0];
    
    if ($mod > 1 && $mod < 5 && ($md100 < 10 || $md100 > 20)) 
      return $variants[1];
    
    return $variants[2];
  }
}
