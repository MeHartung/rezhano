<?php

namespace StoreBundle\Util;
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 13.03.18
 * Time: 18:32
 */
class DateFormatter
{

  /**
   * @param $date string|\DateTime
   * @return string
   */
  public static function format($date)
  {
    if(!$date instanceof \DateTime)
    {
      $date = date_create($date);
    }

    $month = [
      1=>'января', "февраля", "марта",
      "апреля", "мая", "июня",
      "июля", "августа", "сентября",
      "октября", "ноября", "декабря"
    ];

    $date = sprintf("%s %s %s", $date->format('d'), $month[(int)$date->format('m')], $date->format('Y'));

    return (string)$date;
  }

  public static function formatMonth($date)
  {
    if(!$date instanceof \DateTime)
    {
      $date = date_create($date);
    }

    $month = [
      1=>'январь', "февраль", "март",
      "апрель", "май", "июнь",
      "июль", "август", "сентябрь",
      "октябрь", "ноябрь", "декабрь"
    ];

    $date = sprintf("%s", $month[(int)$date->format('m')]);

    return (string)$date;
  }

}