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
 * Date: 27.09.2017
 * Time: 19:39
 */

namespace App\Sitemap\Extractor;

use StoreBundle\Entity\Text\Article;
use StoreBundle\Entity\Text\News;
use Symfony\Component\Routing\RouterInterface;

class SpecialOfferUrlExtractor extends SluggableUrlExtractor
{
  public function __construct(RouterInterface $router)
  {
    parent::__construct($router, 'special_offers_show', 'weekly', 0.56);
  }

  public function supports($model)
  {
    return $model instanceof News;
  }
}