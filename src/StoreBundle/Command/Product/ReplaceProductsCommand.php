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
  private $map = [
    'Бреби' => 'Сыр РЕЖАНО Бреби',
    'Буррата ' => 'Сыр РЕЖАНО Буррата вес (Россия)',
    'Буше' => 'Сыр РЕЖАНО Буше десерт с бел плес 45-60%  вес (Россия)',
    'Буше с инжиром' => '',
    'Дрим блю' => 'Сыр РЕЖАНО Дрим блю мяг с голуб плес 45-60% в/у вес (Россия)',
    'Зерно на экскурсии' => '',
    'Камамбер' => 'Сыр РЕЖАНО Камамбер мяг с бел плес 45-60% в вес (Россия)',
    'Капра' => 'Сыр РЕЖАНО Мантова',
    'Качотта без добавок' => 'Сыр РЕЖАНО 40% в/у вес (Россия)',
    'Качотта в итальянских травах' => 'Сыр РЕЖАНО в итальянских травах 40% в/у вес (Россия)',
    'Качотта с паприкой' => 'Сыр РЕЖАНО с паприкой 40% в/у вес (Россия)',
    'Качотта с прованскими травами' => 'Сыр РЕЖАНО с прованскими травами 40% в/у вес (Россия)',
    'Качотта с розовым перцем' => 'Сыр РЕЖАНО с розовым перцем 40% в/у вес (Россия)',
    'Качотта с сушеным томатом' => 'Сыр с мытой коркой.',
    'Монтазио 2 месяца' => 'Сыр РЕЖАНО Монтазио п/тв с мягк вкус 47% в/у вес (Россия)',
    'Монте Блун' => 'Сыр РЕЖАНО Монте блун',
    'Моцарелла' => '',
    'Режано 6 месяцев' => 'Сыр РЕЖАНО Честер тв пикантный 47% в/у вес (Россия)',
    'Рикотта' => 'Сыр РЕЖАНО Рикотта вес (Россия)',
    'Скаморца' => 'Сыр РЕЖАНО Скаморца 45% в/у вес (Россия)',
    'Стинки' => 'Сыр РЕЖАНО Стинки 38% в/у вес (Россия)',
    'Страчателла' => 'Сыр РЕЖАНО Страчателла вес (Россия)',
    'Тревизо' => 'Сыр РЕЖАНО Тревизо',
    'Шемудин' => '',
    'Азоло' => 'Сыр РЕЖАНО Азоло'
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
    
    /** @var string $newName */
    foreach ($this->map as $oldName => $newName)
    {
      /** @var Product $oldProduct */
      $oldProduct = $em->getRepository(Product::class)->findOneBy([
        'name' => $oldName
      ]);
      
      /** @var Product $newProduct */
      $newProduct = $em->getRepository(Product::class)->findOneBy([
        'name' => $newName
      ]);
      
      if ($newName === '' && $oldProduct)
      {
        $oldProduct->setName($oldName . '(архивный)');
        $oldProduct->setSlug(null);
        $oldProduct->setPublished(false);
        $oldProduct->setPublicationAllowed(false);
        
        $em->persist($oldProduct);
        continue;
      }
      
      if (!$oldProduct)
      {
        $output->writeln('Old product ' . $oldName . ' not found');
        continue;
      }
      
      if (!$newProduct)
      {
        $output->writeln('New product ' . $newName . ' not found');
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
        $output->writeln($exception->getMessage());
        break;
      }
      try
      {
        $newProduct->setImages($this->copyGallery($oldProduct, $newProduct, $output));
      } catch (\Exception $exception)
      {
        $output->writeln($exception->getMessage());
        break;
      }
      
      $newProduct->setName($oldProduct->getName());
      $newProduct->setDescription($oldProduct->getDescription());
      $newProduct->setStocks($oldProduct->getStocks());
      $newProduct->setTaxons($oldProduct->getTaxons());
      $newProduct->setSlug($oldProduct->getSlug());
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
      
      $oldProduct->setName($oldName . '(архивный)');
      $oldProduct->setSlug(null);
      $oldProduct->setPublished(false);
      $oldProduct->setPublicationAllowed(false);
      $output->writeln("$oldName=>$newName was transformed. New id: {$newProduct->getId()}");
      
      $em->persist($newProduct);
      $em->persist($oldProduct);
    }
    
    $em->flush();
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
}