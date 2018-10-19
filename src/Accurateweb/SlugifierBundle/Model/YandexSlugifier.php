<?php

namespace Accurateweb\SlugifierBundle\Model;

class YandexSlugifier implements SlugifierInterface
{
  /**
   * Игнорирует параметр $separator. Всегда использует "-".
   *
   * @inheritdoc
   */
  public function slugify($text, $separator='-')
  {

    //словарь транслитерации по правилам Яндекса.
    $translitArr = array(
      'А' => 'A', 'а' => 'a',
      'Б' => 'B', 'б' => 'b',
      'В' => 'V', 'в' => 'v',
      'Г' => 'G', 'г' => 'g',
      'Д' => 'D', 'д' => 'd',
      'Е' => 'E', 'е' => 'e',
      'Ё' => 'YO', 'ё' => 'yo',
      'Ж' => 'ZH', 'ж' => 'zh',
      'З' => 'Z', 'з' => 'z',
      'И' => 'I', 'и' => 'i',
      'Й' => 'J', 'й' => 'j',
      'К' => 'K', 'к' => 'k',
      'Л' => 'L', 'л' => 'l',
      'М' => 'M', 'м' => 'm',
      'Н' => 'N', 'н' => 'n',
      'О' => 'O', 'о' => 'o',
      'П' => 'P', 'п' => 'p',
      'Р' => 'R', 'р' => 'r',
      'С' => 'S', 'с' => 's',
      'Т' => 'T', 'т' => 't',
      'У' => 'U', 'у' => 'u',
      'Ф' => 'F', 'ф' => 'f',
      'Х' => 'H', 'х' => 'h',
      'Ц' => 'C', 'ц' => 'c',
      'Ч' => 'CH', 'ч' => 'ch',
      'Ш' => 'SH', 'ш' => 'sh',
      'Щ' => 'SHCH', 'щ' => 'shch',
      'Ъ' => '', 'ъ' => '',
      'Ы' => 'Y', 'ы' => 'y',
      'Ь' => '', 'ь' => '',
      'Э' => 'EH', 'э' => 'eh',
      'Ю' => 'YU', 'ю' => 'yu',
      'Я' => 'YA', 'я' => 'ya',

      ' ' => '-'
    );

    if (!strtr($text, $translitArr))
    {
      $text = preg_replace('/[^-a-z0-9]+/', '-', $text); //заменяем
    }
    else
    {
      $text = strtr($text, $translitArr); //заменяем
      $text = strtolower($text); //ниж. рег истр

      $text = preg_replace('/\-+/', '-', $text); //т.к. n пробелов = n дефисов.
      $text = preg_replace('/[^-a-z0-9_]/', '', $text); // всё что не то, то убираем
      $text = trim($text, '-'); //убираем лишние дефисы в начале и конце
    }
    return $text;
  }
}