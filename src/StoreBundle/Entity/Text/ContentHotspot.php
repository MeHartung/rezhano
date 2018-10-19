<?php

namespace StoreBundle\Entity\Text;

use Accurateweb\ContentHotspotBundle\Model\ContentHotspot as BaseContentHotspot;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="content_hotspot")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class ContentHotspot extends BaseContentHotspot
{

}