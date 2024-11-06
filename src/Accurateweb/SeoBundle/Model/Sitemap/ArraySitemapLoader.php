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
 * Date: 26.10.2017
 * Time: 16:48
 */

namespace Accurateweb\SeoBundle\Model\Sitemap;

use Symfony\Component\Finder\Tests\Iterator\Iterator;

/**
 * Loads a collection of sitemap urls from an array of SitemapUrl
 *
 * @package Accurateweb\SeoBundle\Model\Sitemap
 */
class ArraySitemapLoader implements SitemapLoaderInterface
{
  private $urls;

  public function __construct($urls)
  {
    $this->urls = array();

    if (null !== $urls)
    {
      if (!is_array($urls) && !$urls instanceof \Iterator)
      {
        throw new \InvalidArgumentException('Argument must be an array or an instance of Iterator');
      }

      foreach ($urls as $url)
      {
        if (!$url instanceof SitemapUrl)
        {
            throw new \InvalidArgumentException('Every url must be an instance of SitemapUrl');
        }

        $this->add($url);
      }
    }
  }

  public function add(SitemapUrl $url)
  {
    $this->urls[] = $url;
  }

  public function load()
  {
    return $this->urls;
  }

}