<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Repository\Store\Catalog\Product;

use Doctrine\Common\Collections\Criteria;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Catalog\Product\ProductImage;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

class ProductImageRepository extends SortableRepository
{

}