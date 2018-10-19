<?php

namespace StoreBundle\Twig;


class DateExtension extends \Twig_Extension
{

  public function getFilters ()
  {
    return array(
      new \Twig_SimpleFilter('prepareDate', array($this, 'prepareDate'), array('needs_environment' => true)),
    );
  }

  /**
   * @param \Twig_Environment $environment
   * @param \DateTime|string $content
   * @param string $format
   * @return string
   */
  public function prepareDate (\Twig_Environment $environment, $content, $format = 'j F Y H:i')
  {
    $months = [1 => 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
    $shortMonths = [1 => 'янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];

    if ($content instanceof \DateTime)
    {
      $date = $content;
    }
    elseif (date_create($content))
    {
      $date = date_create($content);

      if (!$date)
      {
        return $content;
      }
    }
    else
    {
      return $content;
    }

    $month = $date->format('n');
    $format = str_replace('F', $months[$month], $format);
    $format = str_replace('M', $shortMonths[$month], $format);
    $return = $date->format($format);

    return $return;

  }

}