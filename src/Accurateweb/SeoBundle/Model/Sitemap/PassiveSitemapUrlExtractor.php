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
 * Time: 17:05
 */

namespace Accurateweb\SeoBundle\Model\Sitemap;

class PassiveSitemapUrlExtractor implements SitemapUrlExtractorInterface
{
  public function supports($model)
  {
    return $model instanceof SitemapUrl;
  }

  public function extract($model)
  {
    return $model;
  }
}