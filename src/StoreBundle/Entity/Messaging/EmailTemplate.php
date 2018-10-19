<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Entity\Messaging;

use Accurateweb\EmailTemplateBundle\Entity\EmailTemplate as BaseEmailTemplate;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of EmailTemplate
 *
 * @package StoreBundle\Entity\Messaging
 *
 * @ORM\Entity()
 * @ORM\Table(name="email_templates")
 */
class EmailTemplate extends BaseEmailTemplate
{
}