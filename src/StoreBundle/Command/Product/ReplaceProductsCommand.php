<?php


namespace StoreBundle\Command\Product;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\SEO\ProductRedirectRule;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Media\Store\Catalog\Product\ProductImage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class ReplaceProductsCommand extends ContainerAwareCommand
{
  private $productMap = [];
  
  /*private $map = [
    '120c0ddc-5ff2-11e8-9ff4-34e80002c4eb' => [
      'search' => '9600f9f4-5442-11e9-912f-f3d400142450',
      'new' => 'Режано Бреби'
    ],
    '643bc9f0-8c97-11e7-7a6c-d2a9000525ea' => [
      'search' => '0cadab27-43be-11e9-9ff4-34e8000a7012',
      'new' => 'Режано Буррата'
    ],
    '4e8d4952-8c97-11e7-6b01-4b1d00053bb6' => [
      'search' => '5b658466-4555-11e9-9ff4-3150000c47cc',
      'new' => 'Режано Буше'
    ],
    'e235092b-40ef-11e9-9107-504800086531' => '',
    'Дрим блю' => [
      'search' => 'Сыр РЕЖАНО Дрим блю мяг с голуб плес 45-60% в/у вес (Россия)',
      'new' => 'Режано Дрим блю'
    ],
    'Зерно на экскурсии' => '',
    '5b6daf57-8c97-11e7-7a69-8f5500024b9e' => [
      'search' => '634eb919-4555-11e9-9ff4-3150000c485a',
      'new' => 'Режано Камамбер'
    ],
    'cfe6b2e1-08f0-11e9-9ff4-34e80003fd0e' => [
      'search' => '9c92e594-55d5-11e9-9107-5048000c2d32', # капра в МС: 8df287f5-5442-11e9-9109-f8fc0014866a
      'new' => 'Режано Мантова'
    ],
    '3c17aabe-8c97-11e7-7a69-8f550002499d' => [
      'search' => '323517aa-4555-11e9-9ff4-34e8000be5e5',
      'new' => 'Режано'
    ],
    'c80bb82d-3cbf-11e8-9ff4-34e8001e289b' => [
      'search' => 'bcd78eab-543f-11e9-9109-f8fc001529c4',
      'new' => 'Режано в итальянских травах'
    ],
    '9a3691a9-8c97-11e7-7a31-d0fd00054e4d' => [
      'search' => '782d4de7-4555-11e9-9109-f8fc000c3477',
      'new' => 'Режано с паприкой'
    ],
    '6b581329-8c96-11e7-7a6c-d2a90004fca2' => [
      'search' => '84f2de81-4555-11e9-9ff4-34e8000d4567',
      'new' => 'Режано с прованскими травами'
    ],
    '8c10b520-8c97-11e7-7a31-d0fd00054caa' => [
      'search' => '70c11a6e-4555-11e9-9107-5048000c0327',
      'new' => 'Режано с розовым перцем'
    ],
    '914e0ff1-fde0-11e8-9107-5048000212f0' => [
      'search' => '8b0e4915-4555-11e9-912f-f3d4000c3e62',
      'new' => 'Режано с томатом'
    ],
    'c6db90c9-8c97-11e7-6b01-4b1d000550d9' => [
      'search' => '4c434f76-4555-11e9-9109-f8fc000c31ad',
      'new' => 'Режано Монтазио'
    ],
    'a3e2dd5f-8c97-11e7-6b01-4b1d000549ad' => [
      'search' => 'aab529b4-5442-11e9-9107-504800144d24',
      'new' => 'Режано Монте блун'
    ],
    '6b61ab47-8c97-11e7-7a34-5acf0004d3ad' => [
      'search' => '17c88d59-43be-11e9-9ff4-34e8000a7065',
      'new' => 'Режано Моцарелла'
    ],
    'e532e90e-fb72-11e7-7a6c-d2a9001976aa' => [
      'search' => '50a5d022-4555-11e9-9109-f8fc000c0137',
      'new' => 'Режано Честер'
    ],
    '7216a174-8c97-11e7-7a34-5acf0004d4c6' => [
      'search' => '27593a9d-43be-11e9-912f-f3d4000899ce',
      'new' => 'Режано Рикотта'
    ],
    '45804d30-8c97-11e7-7a69-971100029963' => [
      'search' => '68943094-4555-11e9-9ff4-34e8000bfcfc',
      'new' => 'Режано Скаморца'
    ],
    '5d1aaed0-a213-11e8-9107-50480005caaa' => [
      'search' => '46514e4e-4555-11e9-9107-5048000cbba6',
      'new' => 'Режано Стинки'
    ],
    '794cf096-8c97-11e7-7a69-971100029ed2' => [
      'search' => '2edf409f-5440-11e9-912f-f3d4001400cc',
      'new' => 'Режано Страчателла'
    ],
    'b31fa27e-8c97-11e7-7a34-5acf0004e197' => [
      'search' => 'a1573151-5442-11e9-9109-f8fc00148750',
      'new' => 'Режано Тревизо'
    ],
    'ac2b107e-e8bc-11e7-7a6c-d2a900222607' => '',
    '00022' => [
      'search' => 'bc4c35b5-5442-11e9-912f-f3d400142628',
      'new' => 'Режано Азоло'
    ],
  ];*//*  private $map = [
    'Бреби' => [
      'search' => 'Сыр РЕЖАНО Бреби',
      'new' => 'Режано Бреби'
    ],
    'Буррата ' => [
      'search' => 'Сыр РЕЖАНО Буррата вес (Россия)',
      'new' => 'Режано Буррата'
    ],
    'Буше' => [
      'search' => 'Сыр РЕЖАНО Буше десерт с бел плес 45-60%  вес (Россия)',
      'new' => 'Режано Буше'
    ],
    'Буше с инжиром' => '',
    'Дрим блю' => [
      'search' => 'Сыр РЕЖАНО Дрим блю мяг с голуб плес 45-60% в/у вес (Россия)',
      'new' => 'Режано Дрим блю'
    ],
    'Зерно на экскурсии' => '',
    'Камамбер' => [
      'search' => 'Сыр РЕЖАНО Камамбер мяг с бел плес 45-60% в вес (Россия)',
      'new' => 'Режано Камамбер'
    ],
    'Капра' => [
      'search' => 'Сыр РЕЖАНО Мантова',
      'new' => 'Режано Мантова'
    ],
    'Качотта без добавок' => [
      'search' => 'Сыр РЕЖАНО 40% в/у вес (Россия)',
      'new' => 'Режано'
    ],
    'Качотта в итальянских травах' => [
      'search' => 'Сыр РЕЖАНО в итальянских травах 40% в/у вес (Россия)',
      'new' => 'Режано в итальянских травах'
    ],
    'Качотта с паприкой' => [
      'search' => 'Сыр РЕЖАНО с паприкой 40% в/у вес (Россия)',
      'new' => 'Режано с паприкой'
    ],
    'Качотта с прованскими травами' => [
      'search' => 'Сыр РЕЖАНО с прованскими травами 40% в/у вес (Россия)',
      'new' => 'Режано с прованскими травами'
    ],
    'Качотта с розовым перцем' => [
      'search' => 'Сыр РЕЖАНО с розовым перцем 40% в/у вес (Россия)',
      'new' => 'Режано с розовым перцем'
    ],
    'Качотта с сушеным томатом' => [
      'search' => 'Сыр РЕЖАНО с томатом 40% в/у вес (Россия)',
      'new' => 'Режано с томатом'
    ],
    'Монтазио 2 месяца' => [
      'search' => 'Сыр РЕЖАНО Монтазио п/тв с мягк вкус 47% в/у вес (Россия)',
      'new' => 'Режано Монтазио'
    ],
    'Монте Блун' => [
      'search' => 'Сыр РЕЖАНО Монте блун',
      'new' => 'Режано Монте блун'
    ],
    'Моцарелла' => [
      'search' => 'Сыр РЕЖАНО Моцарелла вес (Россия)',
      'new' => 'Режано Моцарелла'
    ],
    'Режано 6 месяцев' => [
      'search' => 'Сыр РЕЖАНО Честер тв пикантный 47% в/у вес (Россия)',
      'new' => 'Режано Честер'
    ],
    'Рикотта' => [
      'search' => 'Сыр РЕЖАНО Рикотта вес (Россия)',
      'new' => 'Режано Рикотта'
    ],
    'Скаморца' => [
      'search' => 'Сыр РЕЖАНО Скаморца 45% в/у вес (Россия)',
      'new' => 'Режано Скаморца'
    ],
    'Стинки' => [
      'search' => 'Сыр РЕЖАНО Стинки 38% в/у вес (Россия)',
      'new' => 'Режано Стинки'
    ],
    'Страчателла' => [
      'search' => 'Сыр РЕЖАНО Страчателла вес (Россия)',
      'new' => 'Режано Страчателла'
    ],
    'Тревизо' => [
      'search' => 'Сыр РЕЖАНО Тревизо',
      'new' => 'Режано Тревизо'
    ],
    'Шемудин' => '',
    'Азоло' => [
      'search' => 'Сыр РЕЖАНО Азоло',
      'new' => 'Режано Азоло'
    ],
  ];*/
  
  private $map = [
    '00092' => [
      'search' => '27333',
      'new' => 'Режано Бреби'
    ],
    '00020 ' => [
      'search' => '00533',
      'new' => 'Режано Буррата'
    ],
    '00034' => [
      'search' => '23317',
      'new' => 'Режано Буше'
    ],
    '27000010028090' => '',
    '03144' => [
      'search' => '23333',
      'new' => 'Режано Дрим блю'
    ],
    '2700001002462' => '',
    '00032' => [
      'search' => '23319',
      'new' => 'Режано Камамбер'
    ],
    '00091' => [
      'search' => '25455',
      'new' => 'Режано Мантова'
    ],
    '00004' => [
      'search' => '23326',
      'new' => 'Режано'
    ],
    '00002' => [
      'search' => '29433',
      'new' => 'Режано в итальянских травах'
    ],
    '00006' => [
      'search' => '23328',
      'new' => 'Режано с паприкой'
    ],
    '00008' => [
      'search' => '23329',
      'new' => 'Режано с прованскими травами'
    ],
    '00007' => [
      'search' => '23324',
      'new' => 'Режано с розовым перцем'
    ],
    '27000010027818' => [
      'search' => '23330',
      'new' => 'Режано с томатом'
    ],
    '00017' => [
      'search' => '23332',
      'new' => 'Режано Монтазио'
    ],
    '00015' => [
      'search' => '28333',
      'new' => 'Режано Монте блун'
    ],
    '00013' => [
      'search' => '00532',
      'new' => 'Режано Моцарелла'
    ],
    '00012' => [
      'search' => '23318',
      'new' => 'Режано Честер'
    ],
    '00025' => [
      'search' => '00531',
      'new' => 'Режано Рикотта'
    ],
    '00009' => [
      'search' => '23323',
      'new' => 'Режано Скаморца'
    ],
    '00677' => [
      'search' => '23331',
      'new' => 'Режано Стинки'
    ],
    '00019' => [
      'search' => '00535',
      'new' => 'Режано Страчателла'
    ],
    '00016' => [
      'search' => '25334',
      'new' => 'Режано Тревизо'
    ],
    '00026' => '',
    '00022' => [
      'search' => '23536',
      'new' => 'Режано Азоло'
    ],
  ];
  
  public function configure()
  {
    $this
      ->setName('products:replace');
  }
  
  public function execute(InputInterface $input, OutputInterface $output)
  {
    $doctrine = $this->getContainer()->get('doctrine');
    $em = $doctrine->getManager();
    
    /** @var string $newData */
    foreach ($this->map as $oldProductImportKey => $newData)
    {
      /** @var Product $oldProduct */
      $oldProduct = $em->getRepository(Product::class)->findOneBy([
        'externalCode' => $oldProductImportKey
      ]);
      
      if (!$oldProduct)
      {
        $output->writeln('Old product ' . $oldProductImportKey . ' not found');
        continue;
      }
      
      # если не массив, то снимаем старый товар с публикации
      if (!is_array($newData) && $oldProduct)
      {
        $oldProduct = $this->unpublishProduct($oldProduct);
        $em->persist($oldProduct);
        $output->writeln("Product {$oldProduct->getId()} unpublished");
        continue;
      }
      
      $newName = $newData['new'];
      $newProductImportKey = $newData['search'];
      
      /** @var Product $newProduct */
      $newProduct = $em->getRepository(Product::class)->findOneBy([
        'externalCode' => $newProductImportKey
      ]);
      
      if (!$newProduct)
      {
        $output->writeln('New product ' . $newProductImportKey . ' not found.');
        continue;
      }
      
      try
      {
        $path = $this->copyMainImage($newProduct, $oldProduct);
        
        if ($path !== null)
        {
          $newProduct->setTeaserImageFile($path);
        }
      } catch (\Exception $exception)
      {
        $output->writeln('Не удалось перенсти тизер товара: ' . $exception->getMessage());
      }
      
      try
      {
        $newProduct->setImages($this->copyGallery($oldProduct, $newProduct, $output));
      } catch (\Exception $exception)
      {
        $output->writeln('Не удалось перенсти галерею товара: ' . $exception->getMessage());
      }
      
      # скопирует все значения кроме имени и слага из старого товара
      $newProduct = $this->copyFromProductToProduct($oldProduct, $newProduct, $newName);
      # до того, как изменим старый товар, нужно запомнить его слаг
      $this->addDataToProductsMap($oldProduct, $newProduct);
      # снимем старый товар с публикации и поменяем слаг
      $oldProduct = $this->unpublishProduct($oldProduct);
      
      $output->writeln("{$oldProduct->getMoySkladName()}($oldProductImportKey) was transformed to {$newProduct->getMoySkladName()}($newProductImportKey) with new name {$newName}. Id: {$newProduct->getId()}");
      
      $em->persist($newProduct);
      $em->persist($oldProduct);
    }
    
    $em->flush();
    
    $slugMap = [];
    foreach ($this->productMap as $data)
    {
      $product = $em->find(Product::class, $data['new_id']);
      if (!$product)
      {
        continue;
      }
      
      if ($product->getSlug() !== $data['old_slug'])
      {
        $slugMap[$data['new_id']] = [
          'from' => $data['old_slug'],
          'to' => $product->getSlug(),
          'new_id' => $product->getId()
        ];
      }
    }
    
    foreach ($slugMap as $item)
    {
      $redirectRule = new ProductRedirectRule();
      $redirectRule->setSlugFrom($item['from']);
      $redirectRule->setSlugTo($item['to']);
      
      $em->persist($redirectRule);
      $output->writeln(sprintf('Add rule for redirect: /products/%s => /products/%s', $item['from'], $item['to']));
    }
    
    $em->flush();
    
    $output->writeln('Fix slugs...');
    $this->fixSlugs($slugMap, $output, $em);
    
    /* этот код не нужен, т.к. исп. событие pre_http_not_found
    $htaccessData = [];
     
     foreach ($slugMap as $item)
     {
       $htaccessData[] = sprintf('RedirectMatch 301 ^/products/%s /products/%s', $item['from'], $item['to']);
     }
     
     if (count($htaccessData))
     {
       $htaccessDataPath = $this->getContainer()->getParameter('kernel.root_dir') . '/../var/uploads/htaccess_data.txt';
       file_put_contents($htaccessDataPath, implode("\n", $htaccessData));
       $output->writeln('htaccessData in ' . $htaccessDataPath);
     }
     
     $output->writeln(implode("\n", $htaccessData));*/
  }
  
  /**
   * @param Product $oldProduct
   * @param Product $newProduct
   * @return void
   */
  private function addDataToProductsMap(Product $oldProduct, Product $newProduct)
  {
    $this->productMap[] = [
      'old_slug' => $oldProduct->getSlug(), # слаг старого товара
      'new_id' => $newProduct->getId() #  id товара, для которого нужно этот слаг применить
    ];
  }
  
  /**
   * @param Product $oldProduct
   * @param Product $newProduct
   * @param string $newName - новое имя
   * @return Product
   */
  private function copyFromProductToProduct(Product $oldProduct, Product $newProduct, $newName)
  {
    $newProduct->setName($newName);
    $newProduct->setSlug(null);
    $newProduct->setDescription($oldProduct->getDescription());
    $newProduct->setStocks($oldProduct->getStocks());
    $newProduct->setTaxons($oldProduct->getTaxons());
    #$newProduct->setPrice($oldProduct->getPrice());
    $newProduct->setMultiplier($oldProduct->getMultiplier());
    $newProduct->setBundle($oldProduct->getName());
    $newProduct->setUnitWeight($oldProduct->getUnitWeight());
    $newProduct->setPackage($oldProduct->getPackage());
    $newProduct->setSku($oldProduct->getSku());
    $newProduct->setWeight($oldProduct->getWeight());
    $newProduct->setUnits($oldProduct->getUnits());
    $newProduct->setBrand($oldProduct->getBrand());
    $newProduct->setHeight($oldProduct->getHeight());
    $newProduct->setInStock($oldProduct->getInStock());
    $newProduct->setHit($oldProduct->isHit());
    $newProduct->setPublished($oldProduct->isPublished());
    $newProduct->setPackage($oldProduct->getPackage());
    $newProduct->setIsFreeDelivery($oldProduct->isFreeDelivery());
    $newProduct->setWithGift($oldProduct->isWithGift());
    $newProduct->setPublicationAllowed($oldProduct->isPublicationAllowed());
    #$newProduct->setPurchasePrice($oldProduct->getPurchasePrice());
    $newProduct->setVolume($oldProduct->getVolume());
    $newProduct->setNovice($oldProduct->isNovice());
    #$newProduct->setOldPrice($oldProduct->getOldPrice());
    $newProduct->setShortDescription($oldProduct->getShortDescription());
    $newProduct->setSale($oldProduct->isSale());
    $newProduct->setProductAttributeValues($oldProduct->getProductAttributeValues());
    $newProduct->setProductType($oldProduct->getProductType());
    
    return $newProduct;
  }
  
  /**
   * @param $from string
   * @param $to string
   */
  private function copyDir($from, $to)
  {
    $fs = new Filesystem();
    
    # если под товар ещё не создали папку
    if (!$fs->exists($from))
    {
      throw new \RuntimeException('У товара есть изображения в БД, но нет в файловой системе!');
    }
    
    if (!$fs->exists($to))
    {
      $fs->mkdir($to);
    }
    
    $fs->mirror($from, $to);
  }
  
  /**
   * @param Product $newProduct
   * @param Product $oldProduct
   * @return null|string
   */
  private function copyMainImage(Product $newProduct, Product $oldProduct)
  {
    $oldTeaser = $oldProduct->getTeaserImageFile();
    if ($oldTeaser && is_file($this->getContainer()->getParameter('kernel.root_dir') . '/../web/uploads/' . $oldTeaser))
    {
      $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
      
      $fullPath = $oldTeaser;
      $dotPosition = strrpos($fullPath, '.');
      $ext = substr($fullPath, $dotPosition);
      $fullPathWithoutExt = str_replace($ext, '_' . $newProduct->getId(), $fullPath);
      $path = $fullPathWithoutExt . $ext;
      
      $fs = new Filesystem();
      $fs->copy($rootDir . '/../web/uploads/' . $oldTeaser, $rootDir . '/../web/uploads/' . $path);
      $fs->copy($rootDir . '/../var/uploads/' . $oldTeaser, $rootDir . '/../var/uploads/' . $path);
      
      # путь до папки с превью у старого товара
      $fullOldPathWithoutExt = str_replace($ext, '', $fullPath);
      
      $fs->mirror($rootDir . '/../web/uploads/' . $fullOldPathWithoutExt,
        $rootDir . '/../web/uploads/' . $fullPathWithoutExt);
      
      return $path;
    }
    
    return null;
  }
  
  /**
   * @param Product $oldProduct
   * @param Product $newProduct
   * @return ArrayCollection
   */
  private function copyGallery(Product $oldProduct, Product $newProduct, OutputInterface $output)
  {
    $images = $oldProduct->getImages();
    
    $cloned_images = new ArrayCollection();
    
    if ($images->count() === 0)
    {
      return $cloned_images;
    }
    
    $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
    
    $webDir = $rootDir . '/../web/uploads/';
    $varDir = $rootDir . '/../var/uploads/';
    
    $newPath = 'product-photo/' . $newProduct->getId();
    $oldPath = 'product-photo/' . $oldProduct->getId();
    
    try
    {
      $this->copyDir($webDir . $oldPath, $webDir . $newPath);
      $this->copyDir($varDir . $oldPath, $varDir . $newPath);
    } catch (\RuntimeException $exception)
    {
      $output->writeln($exception->getMessage());
      return $cloned_images;
    }
    
    
    /** @var ProductImage $image */
    foreach ($images as $image)
    {
      $cloned_image = clone $image;
      $sourceId = $cloned_image->getResourceId();
      $newSourceId = str_replace("/{$oldProduct->getId()}/", "/{$newProduct->getId()}/", $sourceId);
      
      $cloned_image->setResourceId($newSourceId);
      $cloned_image->setProduct($newProduct);
      $cloned_images->add($cloned_image);
    }
    
    return $cloned_images;
  }
  
  /**
   * @param Product $oldProduct
   * @return Product
   */
  private function unpublishProduct(Product $oldProduct): Product
  {
    $oldProduct->setName($oldProduct->getName() . ' (архивный)');
    # иначе получим кучу "kachotta-arhivnyj"
    $oldProduct->setSlug($oldProduct->getSlug() . '-archive');
    $oldProduct->setPublished(false);
    $oldProduct->setPublicationAllowed(false);
    
    return $oldProduct;
  }
  
  /**
   * Фикс слагов для товаров
   * Делается таким образом, чтобы товары, которые только что заполнили, имели нормальный слаг без порядкового номера
   *
   * @param $products
   * @param OutputInterface $output
   * @param EntityManagerInterface $em
   */
  private function fixSlugs($products, OutputInterface $output, EntityManagerInterface $em)
  {
    $repo = $em->getRepository(Product::class);
    foreach ($products as $product)
    {
      $actualProductId = $product['new_id'];
      $invalidSlugProducts = $repo->findBy([
        'slug' => $product['to']
      ]);
      
      if (count($invalidSlugProducts) > 1)
      {
        $output->writeln(count($invalidSlugProducts) . " products with slug {$product['to']} was found.");
        
        $num = 1;
        /** @var Product $product */
        foreach ($invalidSlugProducts as $productObj)
        {
          # для главного(опубликованного) товара слаг изм. не должен
          if ((int) $productObj->getId() === (int) $actualProductId)
          {
            continue;
          }
          
          $oldSlug = $productObj->getSlug();
          $productObj->setSlug($productObj->getSlug() . '-' . $num);
          ++$num;
          
          $em->persist($productObj);
          
          $mess = sprintf('For product with id %s slug changed: %s => %s.', $productObj->getId(),
            $oldSlug, $productObj->getSlug());
          $output->writeln($mess);
        }
        
        $em->flush();
      } else
      {
        $output->writeln("Products with duplicated slug {$product['to']} was not found.");
      }
    }
  }
}