<?php

namespace StoreBundle\Synchronization\FromMoySklad\Model\Datasource;

use Accurateweb\MoyskladIntegrationBundle\Exception\MoyskladException;
use Accurateweb\SettingBundle\Model\Setting\SettingInterface;
use Accurateweb\MoyskladIntegrationBundle\Model\MoyskladManager;
use Accurateweb\SlugifierBundle\Model\SlugifierInterface;
use Accurateweb\SynchronizationBundle\Model\Datasource\Base\BaseDataSource;
use Doctrine\ORM\EntityManagerInterface;
use MoySklad\Entities\Folders\ProductFolder;
use MoySklad\Entities\Products\Product;
use MoySklad\Exceptions\RequestFailedException;
use MoySklad\Lists\EntityList;
use MoySklad\MoySklad;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class MoySkladSource extends BaseDataSource
{
  private
    $em, $moySkladLogin, $moySkladPassword,
    $kernelRootDir, $dispatcher, $slugifierYandex,
    $logger;
  protected $moyskladManager;
  
  /**
   * Поддериживаемые директории товаров (в терминологии моего склада группы - это папки)
   * @var array
   */
  private $supportFolders = [
    'Сыр в кг',
    'Подарочные корзины'
  ];
  
  /**
   * MoySkladSource constructor.
   *
   * @param array $options
   * @param $to
   * @param EntityManagerInterface $entityManager
   * @param string $moySkladLogin
   * @param string $moySkladPassword
   * @param string $sevenSecondsApiKey
   */
  public function __construct(array $options = array(), $to,
                              EntityManagerInterface $entityManager,
                              SettingInterface $moySkladLogin, SettingInterface $moySkladPassword,
                              $kernelRootDir, EventDispatcherInterface $dispatcher,
                              SlugifierInterface $sluggable, LoggerInterface $logger,
                              MoyskladManager $moyskladManager)
  {
    parent::__construct($options);
    $this->em = $entityManager;
    
    $this->moySkladLogin = $moySkladLogin->getValue();
    $this->moySkladPassword = $moySkladPassword->getValue();
    $this->moyskladManager = $moyskladManager;
    $this->kernelRootDir = $kernelRootDir;
    $this->dispatcher = $dispatcher;
    
    $this->slugifierYandex = $sluggable;
    $this->logger = $logger;
  }
  
  /**
   *
   * @param $from string
   * @param null $to
   * @return string|null
   */
  public function get($from, $to = null, $em = null)
  {
    try
    {
      $sklad = MoySklad::getInstance($this->moySkladLogin, $this->moySkladPassword);
    } catch (\Exception $exception)
    {
      #$this->logger->addError('Products list not uploaded from MoySklad:' . "\n" .  $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString());
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Can\'t login in MoySklad:' . "\n" . $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString())
      );
      return null;
    }

    $folders = $this->getFolders();
    $folderNames = [];

    foreach ($folders as $folder)
    {
      $folderNames[] = $folder->name;
    }


    /**
     * Все товары с моего склада
     *
     * @var EntityList $moySkladProducts
     */
    try
    {
      $moySkladProducts = Product::query($sklad)->getList();
    } catch (MoyskladException $e)
    {
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent((sprintf('%s.', $e->getMessage())))
      );
      $this->logger->error(sprintf('[%s]%s. %s', $e->getCode(), $e->getMessage(), $e->getInfo()));
      
      return null;
    } catch (RequestFailedException $e)
    {
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent((sprintf('%s. %s', $e->getMessage(), $e->getDump())))
      );
      
      $this->logger->error(sprintf('[%s]%s. %s', $e->getCode(), $e->getMessage(), $e->getDump()));
      return null;
    } catch (\Exception $exception)
    {
      $this->logger->error('Products list not uploaded from MoySklad:' . "\n" .  $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString());
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Products list not uploaded from MoySklad:' . "\n" . $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString())
      );
      return null;
    }
    
    if ($moySkladProducts->count() === 0)
    {
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Товаров не найдено!')
      );
      
      return;
    }
  
    $fromMySkladProductsInDb = $this->em->getRepository('StoreBundle:Store\Catalog\Product\Product')
      ->createQueryBuilder('p')
      ->select('p.externalCode')
      ->where('p.externalCode IS NOT NULL')
      ->getQuery()->getResult();
    
    $foundInDbProductsCodes = [];
    if($fromMySkladProductsInDb !== null)
    {
      if(is_array($fromMySkladProductsInDb))
      {
        if(count($fromMySkladProductsInDb) > 0)
        {
          /** @var array $item */
          foreach ($fromMySkladProductsInDb as $item)
          {
            $foundInDbProductsCodes[] = $item['externalCode'];
          }
        }
      }
    }
    
    $moySkladProductsAsArray = [];
    
    /**
     * Преобразуем в массив
     *
     * @var Product $product
     */
    foreach ($moySkladProducts->toArray() as $key => $product)
    {
      # нельзя взять и отфильтовать по папке
      # возможно, стоит заменить на FilterIterator
      $folderDataHref = null;
  
      if (isset($product->relations->productFolder->fields->meta->href))
      {
        $folderDataHref = $product->relations->productFolder->fields->meta->href;
    
      } else
      {
        continue;
      }
  
      try
      {
        $folderData = $sklad->getClient()->get($folderDataHref);
      } catch (MoyskladException $e)
      {
        $this->dispatcher->dispatch(
          'aw.sync.order_event.message',
          new GenericEvent((sprintf('%s.', $e->getMessage())))
        );
        $this->logger->error(sprintf('[%s]%s. %s', $e->getCode(), $e->getMessage(), $e->getInfo()));
        continue;
      } catch (RequestFailedException $e)
      {
        $this->dispatcher->dispatch(
          'aw.sync.order_event.message',
          new GenericEvent((sprintf('%s.', $e->getMessage())))
        );
    
        $errorsMessage = '';
        if (isset($e->getDump()['response']->errors))
        {
          foreach ($e->getDump()['response']->errors as $error)
          {
            $errorsMessage .= sprintf('[%s]%s.', $error->code, $error->error) . "\n";
          }
        }
    
        $this->logger->error(sprintf('[%s]%s.', $e->getCode(), $errorsMessage));
        throw new \Exception($errorsMessage);
      } catch (\Exception $exception)
      {
        $this->logger->error('Products list not loaded from MoySklad:' . "\n" . $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString());
        $this->dispatcher->dispatch(
          'aw.sync.order_event.message',
          new GenericEvent('Products list not loaded from MoySklad:' . "\n" . $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString())
        );
        continue;
      }

      if(!in_array($folderData->name, $folderNames))
      {
        continue;
      }
      
      $now = new \DateTime('now');
      
      $salePrice = 0;
      $wholesalePrice = 0;
  
      $article = $this->slugifierYandex->slugify($product->name);
      if(isset($product->barcodes))
      {
        if(count($product->barcodes) > 0)
        {
          $article = $product->barcodes[0];
        }
      }
      
      foreach ($product->salePrices as $price)
      {
        if ($price->priceType == 'Цена продажи')
        {
          $salePrice = $price->value / 100;
        }
        
        if ($price->priceType == 'Оптовый')
        {
          $wholesalePrice = $price->value / 100;
        }
      }
      # если есть такой товар, то не трогать:
      # Наименование, публикацию, описание, краткое описание, артикул, вес и условный вес
      if(in_array($product->code, $foundInDbProductsCodes) === true)
      {
        $productDb = $this->em->getRepository('StoreBundle:Store\Catalog\Product\Product')->findOneBy(
          [
            'externalCode' => $product->code
          ]
        );
        
      $moySkladProductsAsArray[$key] = [
          'name' => $productDb->getName(),
          'external_code' => $product->code,
          'wholesale_price' => $wholesalePrice,  # оптовая
          'price' => $salePrice,
          'purchase_price' => $product->buyPrice->value/100,
          'slug' => $this->slugifierYandex->slugify($product->name),
          'created_at' => $now->format('Y-m-d H:i:s'),
          'is_with_gift' => 0,
          'is_publication_allowed' => (int)$productDb->isPublicationAllowed(),
          'published' => (int)$productDb->isPublished(),
          'total_stock' => 100,
          'reserved_stock' => 10,
          'is_free_delivery' => 0,
          'rank' => 0.00,
          'sku' => $article,
          'short_description' => $productDb->getShortDescription(),
          'description' => $productDb->getDescription(),
          'package' => $productDb->getPackage(),
          'unit_weight' => $productDb->getUnitWeight(),
          'bundle' => 0  # обозначает, что товар на стороне МС не составной
        ];
      }else
      {
        $moySkladProductsAsArray[$key] = [
          'external_code' => $product->code,
          'name' => $product->name,
          'wholesale_price' => $wholesalePrice,  # оптовая
          'price' => $salePrice,
          'purchase_price' => $product->buyPrice->value/100,
          'slug' => $this->slugifierYandex->slugify($product->name),
          'created_at' => $now->format('Y-m-d H:i:s'),
          'is_with_gift' => 0,
          'is_publication_allowed' => 1,
          'published' => 0,
          'total_stock' => 100,
          'reserved_stock' => 10,
          'is_free_delivery' => 0,
          'rank' => 0.00,
          'package' => 1.000,
          'unit_weight' => 1.000,
          'bundle' => 0  # обозначает, что товар на стороне МС не составной
        ];
        $moySkladProductsAsArray[$key]['sku'] = $article;
        /*  if (isset($product->article))
          {
          
          } else
          {
            $moySkladProductsAsArray[$key]['sku'] = $moySkladProductsAsArray[$key]['slug'];
          }*/
  
        if (isset($product->image)) $moySkladProductsAsArray[$key]['image'] = $product->image->meta->href;
        if (isset($product->description))
        {
          # этот костыль необходимо убрать
          # substr что-то непонятное делает со сторкой, после чего из массива не хотит создаваться json
          $short_description = substr($product->description, 0, 50);
    
          $moySkladProductsAsArray[$key]['short_description'] = $product->description; #$short_description;
          $moySkladProductsAsArray[$key]['description'] = $product->description;
    
        } else
        {
          $moySkladProductsAsArray[$key]['description'] = '...';
          $moySkladProductsAsArray[$key]['short_description'] = '...';
        }
  
      }
  
      # всегда обновляем имя в МС
      $moySkladProductsAsArray[$key]['moy_sklad_name'] = $product->name;
      
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent("Product {$folderData->name}/{$product->name} was loaded from MoySklad.")
      );
    }
    
    $productsAsJson = json_encode($moySkladProductsAsArray);

    if ($productsAsJson === false)
    {
      throw new \Exception('Невалидный, написать ошибку!');
    }
    
    file_put_contents($to, $productsAsJson);
    
    return $to;
  }
  
  /**
   * @param $from
   * @param $to
   */
  public function put($from, $to)
  {
  
  }

  /**
   * @return ProductFolder[]
   */
  private function getFolders()
  {
    $folders = [];
    $folderRepository = $this->moyskladManager->getRepository('MoySklad\Entities\Folders\ProductFolder');

    foreach ($this->supportFolders as $supportFolder)
    {
      $folder = $folderRepository->findOneBy(['name' => $supportFolder]);

      if ($folder)
      {
        $folders[] = $folder;

        foreach ($this->getSubFolders($folder) as $subFolder)
        {
          $folders[] = $subFolder;
        }
      }
    }

    return $folders;
  }

  /**
   * @param $folder
   * @return ProductFolder[]
   */
  private function getSubFolders(ProductFolder $folder)
  {
    $folderRepository = $this->moyskladManager->getRepository('MoySklad\Entities\Folders\ProductFolder');
    $folders = $folderRepository->findBy(['pathName' => sprintf('%s%%', $folder->fields->name)]);

    return $folders;
  }
  
}