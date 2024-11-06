<?php

namespace AccurateCommerce\GeoLocation;

use Symfony\Component\HttpFoundation\RequestStack;

class Geo
{
  public $selected_ekb = false;

  /** @var RequestStack */
  private $request_stack;

  private $cookies;

  public function __construct (RequestStack $request_stack)
  {
    $this->dirname = dirname(__file__);
    $this->request_stack = $request_stack;
    $this->cookies = $request_stack->getCurrentRequest()->cookies;

    $this->ip = $this->get_ip();

    $this->charset = null;
    $this->detectEkb();
  }

  public function detectEkb ()
  {
    $data = $this->get_geobase_data();

    if ($this->cookies->has('city') && strcmp($this->cookies->get('city'), 'ekb') == 0 || !$this->cookies->has('city') && isset($data['district']) && strcmp($data['district'], 'Уральский федеральный округ') == 0)
    {
      $this->selected_ekb = true;
    }
  }

  /**
   * функция возвращет конкретное значение из полученного массива данных по ip
   *
   * @param bool $key
   * @param bool $cookie
   *
   * @internal param $string - ключ массива. Если интересует конкретное значение.
   * Ключ может быть равным 'inetnum', 'country', 'city', 'region', 'district', 'lat', 'lng'
   * @internal param $bolean - устанавливаем хранить данные в куки или нет
   * Если true, то в куки будут записаны данные по ip и повторные запросы на ipgeobase происходить не будут.
   * Если false, то данные постоянно будут запрашиваться с ipgeobase
   * @return array OR string - дополнительно читайте комментарии внутри функции.
   */
  public function get_value ($key = false, $cookie = true)
  {
    $key_array = array('inetnum', 'country', 'city', 'region', 'district', 'lat', 'lng');

    if (!in_array($key, $key_array))
    {
      $key = false;
    }

    // если используем куки и параметр уже получен, то достаем и возвращаем данные из куки
    if ($cookie && $this->cookies->has('geobase'))
    {
      $data = unserialize($this->cookies->get('geobase'));
    }
    else
    {
      $data = $this->get_geobase_data();
    }
    if ($key)
    {
      return isset($data[$key]) ? $data[$key] : null;
    } // если указан ключ, возвращаем строку с нужными данными
    else
    {
      return $data;
    } // иначе возвращаем массив со всеми данными
  }

  /**
   * функция получает данные по ip.
   *
   * @return array - возвращает массив с данными
   */
  protected function get_geobase_data ()
  {
    // получаем данные по ip
    $link = 'ipgeobase.ru:7020/geo?ip=' . $this->ip;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    $string = curl_exec($ch);

    // если указана кодировка отличная от windows-1251, изменяем кодировку
    if ($this->charset)
    {
      $string = iconv('windows-1251', $this->charset, $string);
    }

    $data = $this->parse_string($string);

    return $data;
  }

  /**
   * функция парсит полученные в XML данные в случае, если на сервере не установлено расширение Simplexml
   *
   * @return array - возвращает массив с данными
   */
  protected function parse_string ($string)
  {
    $pa['inetnum'] = '#<inetnum>(.*)</inetnum>#is';
    $pa['country'] = '#<country>(.*)</country>#is';
    $pa['city'] = '#<city>(.*)</city>#is';
    $pa['region'] = '#<region>(.*)</region>#is';
    $pa['district'] = '#<district>(.*)</district>#is';
    $pa['lat'] = '#<lat>(.*)</lat>#is';
    $pa['lng'] = '#<lng>(.*)</lng>#is';
    $data = array();
    foreach ($pa as $key => $pattern)
    {
      preg_match($pattern, $string, $out);
      if (isset($out[1]) && $out[1])
      {
        $data[$key] = trim($out[1]);
      }
    }
    return $data;
  }

  /**
   * функция определяет ip адрес по глобальному массиву $_SERVER
   * ip адреса проверяются начиная с приоритетного, для определения возможного использования прокси
   *
   * @return ip-адрес
   */
  protected function get_ip ()
  {
    $ip = false;
    $ipa = [];

    $server = $this->request_stack->getCurrentRequest()->server;
    //    $ipa = $this->request_stack->getCurrentRequest()->getClientIps();

    if ($server->has('HTTP_X_FORWARDED_FOR'))
    {
      $ipa[] = trim(strtok($server->get('HTTP_X_FORWARDED_FOR'), ','));
    }

    if ($server->has('HTTP_CLIENT_IP'))
    {
      $ipa[] = $server->get('HTTP_CLIENT_IP');
    }

    if ($server->has('REMOTE_ADDR'))
    {
      $ipa[] = $server->get('REMOTE_ADDR');
    }

    if ($server->has('HTTP_X_REAL_IP'))
    {
      $ipa[] = $server->get('HTTP_X_REAL_IP');
    }

    // проверяем ip-адреса на валидность начиная с приоритетного.
    foreach ($ipa as $ips)
    {
      //  если ip валидный обрываем цикл, назначаем ip адрес и возвращаем его
      if ($this->is_valid_ip($ips))
      {
        $ip = $ips;
        break;
      }
    }
    return $ip;
  }

  /**
   * функция для проверки валидности ip адреса
   *
   * @param string ip адрес в формате 1.2.3.4
   *
   * @return boolean : true - если ip валидный, иначе false
   */
  protected function is_valid_ip ($ip = NULL)
  {
    if (preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#", $ip))
    {
      return true;
    } // если ip-адрес попадает под регулярное выражение, возвращаем true

    return false; // иначе возвращаем false
  }

}
