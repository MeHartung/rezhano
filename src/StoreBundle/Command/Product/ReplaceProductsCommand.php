<?php


namespace StoreBundle\Command\Product;


use Doctrine\Common\Collections\ArrayCollection;
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
  
  private $map = [
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
      'new' => 'Режано Буше десерт с бел плес'
    ],
    'Буше с инжиром' => '',
    'Дрим блю' => [
      'search' => 'Сыр РЕЖАНО Дрим блю мяг с голуб плес 45-60% в/у вес (Россия)',
      'new' => 'Режано Дрим блю мяг с голуб плес'
    ],
    'Зерно на экскурсии' => '',
    'Камамбер' => [
      'search' => 'Сыр РЕЖАНО Камамбер мяг с бел плес 45-60% в вес (Россия)',
      'new' => 'Режано Камамбер мяг с бел плес'
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
      'new' => 'Режано Монтазио п/тв с мягк вкус'
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
      'new' => 'Режано Честер тв пикантный'
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
    foreach ($this->map as $oldName => $newData)
    {
      /** @var Product $oldProduct */
      $oldProduct = $em->getRepository(Product::class)->findOneBy([
        'name' => $oldName
      ]);
      
      if (!$oldProduct)
      {
        $output->writeln('Old product ' . $oldName . ' not found');
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
      $searchName = $newData['search'];
      
      /** @var Product $newProduct */
      $newProduct = $em->getRepository(Product::class)->findOneBy([
        'name' => $searchName
      ]);
      
      if (!$newProduct)
      {
        $output->writeln('New product ' . $searchName . ' not found');
        continue;
      }
      
      try
      {
        $path = $this->copyMainImage($newProduct, $oldProduct);
        
        if ($path)
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
      $newProduct = $this->copyFromProductToProduct($oldProduct, $newProduct, $newName, $searchName);
      # до того, как изменим старый товар, нужно запомнить его слаг
      $this->addDataToProductsMap($oldProduct, $newProduct);
      # снимем старый товар с публикации и поменяем слаг
      $oldProduct = $this->unpublishProduct($oldProduct);
      
      $output->writeln("$oldName=>$newName was transformed. New id: {$newProduct->getId()}");
      
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
        ];
      }
    }
    
    $htaccessData = [];
    
    foreach ($slugMap as $item)
    {
      $htaccessData[] = sprintf('RedirectMatch 301 ^/products/%s products/%s', $item['from'], $item['to']);
    }
    
    if (count($htaccessData))
    {
      $htaccessDataPath = $this->getContainer()->getParameter('kernel.root_dir') . '/../var/uploads/htaccess_data.txt';
      file_put_contents($htaccessDataPath, implode("\n", $htaccessData));
      $output->writeln('htaccessData in ' . $htaccessDataPath);
    }
    
    $output->writeln(implode("\n", $htaccessData));
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
   * @param string $msName - имя товара в МС
   * @return Product
   */
  private function copyFromProductToProduct(Product $oldProduct, Product $newProduct, $newName, $msName)
  {
    $newProduct->setName($newName);
    $newProduct->setMoySkladName($msName);
    $newProduct->setSlug(null);
    $newProduct->setDescription($oldProduct->getDescription());
    $newProduct->setStocks($oldProduct->getStocks());
    $newProduct->setTaxons($oldProduct->getTaxons());
    $newProduct->setPrice($oldProduct->getPrice());
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
    $newProduct->setPurchasePrice($oldProduct->getPurchasePrice());
    $newProduct->setVolume($oldProduct->getVolume());
    $newProduct->setNovice($oldProduct->isNovice());
    $newProduct->setOldPrice($oldProduct->getOldPrice());
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
  private function unpublishProduct(Product $oldProduct)
  {
    $oldProduct->setName($oldProduct->getName() . ' (архивный)');
    $oldProduct->setSlug(null);
    $oldProduct->setPublished(false);
    $oldProduct->setPublicationAllowed(false);
    
    return $oldProduct;
  }
}