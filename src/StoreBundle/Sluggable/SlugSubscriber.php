<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 28.07.17
 * Time: 13:13
 */

namespace StoreBundle\Sluggable;

use Accurateweb\SlugifierBundle\Model\SlugifierInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class SlugSubscriber implements EventSubscriber
{
  private $slugifier;
  private $validator;

  public function __construct(SlugifierInterface $slugifier, ValidatorInterface $validator)
  {
    $this->slugifier = $slugifier;
    $this->validator = $validator;
  }

  public function getSubscribedEvents()
  {
    return array(
      'prePersist',
      'preUpdate'
    );

  }

  public function prePersist(LifecycleEventArgs $args)
  {
    $this->updateSlug($args);
  }

  public function preUpdate(LifecycleEventArgs $args)
  {
    $this->updateSlug($args);
  }

  /**
   * @param LifecycleEventArgs $args
   */
  public function updateSlug(LifecycleEventArgs $args)
  {
    $object = $args->getObject();

    if ($object instanceof SluggableInterface && !$object->getSlug())
    {
      $this->setObjectSlug($object, (string)$object->getSlugSource());
    }
  }

  private function setObjectSlug(SluggableInterface $object, $name)
  {
    $object->setSlug($this->slugifier->slugify($name));
    $violations = $this->validator->validate($object);

    if (count($violations))
    {
      /** @var ConstraintViolationInterface $violation */
      foreach ($violations as $violation)
      {
        if ($violation->getPropertyPath() === 'slug')
        {
          $i = 1;

          if (preg_match('/.*\-(\d+)$/', $name, $m))
          {
            $i = ((int)$m[1])+1;
            $name = preg_replace('/\-(\d+)$/', '', $name);
          }

          $this->setObjectSlug($object, sprintf('%s-%d', $name, $i));
          break;
        }
      }
    }
  }
}