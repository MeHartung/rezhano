<?php

namespace StoreBundle\Service\Product;

use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Store\Catalog\Product\ProductImage;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class ProductMovingService
{
  private $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  public function moveImage($requestId, $requestPosition)
  {
    $movedFirstImg = $this->em->getRepository(ProductImage::class)->findOneBy(['id' => $requestId]);

    if (is_null($movedFirstImg))
    {
      throw new BadRequestHttpException('Не найдено перемещаемое избражение');
    }

    $positionNow = $movedFirstImg->getPosition();

    if($requestPosition === null)
    {
      throw new BadRequestHttpException('Позиция не м.б. пустой.');
    }

    $product = $movedFirstImg->getProduct();

    if ($product)
    {
      $movedSecondImage = $this->em->getRepository(ProductImage::class)
                           ->findOneBy(['product' => $product, 'position' => $requestPosition]);

      if (is_null($movedSecondImage))
      {
        throw new BadRequestHttpException('Не найдено перемещаемое избражение');
      }

    } else
    {
      throw new BadRequestHttpException('Товар не найден');
    }

    $movedSecondImage->setPosition($positionNow);
    $movedFirstImg->setPosition($requestPosition);

    $this->em->persist($movedSecondImage);
    $this->em->persist($movedFirstImg);
    try
    {
      $this->em->flush();
    }
    catch (\Exception $e)
    {
      throw new \Exception($e->getMessage());
    }

    return [
      'request_img_id' => $requestId,
      'request_img_pos' => $positionNow,
      'now_img_pos' => $movedFirstImg->getPosition()];
  }

}