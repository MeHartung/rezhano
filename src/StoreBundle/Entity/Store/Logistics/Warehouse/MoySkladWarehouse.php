<?php

namespace StoreBundle\Entity\Store\Logistics\Warehouse;

use Doctrine\ORM\Mapping as ORM;
use Accurateweb\MoyskladIntegrationBundle\Model\Logistic\MoySkladWarehouse as Base;

/**
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Logistics\Delivery\Warehouse\MoySkladWarehouseRepository")
 * @ORM\Table()
 */
class MoySkladWarehouse extends Base
{

}