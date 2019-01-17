<?php

namespace StoreBundle\Synchronization\FromMoySklad\Model\Datasource;

use Accurateweb\MoyskladIntegrationBundle\Exception\MoyskladException;
use Accurateweb\SlugifierBundle\Model\SlugifierInterface;
use Accurateweb\SynchronizationBundle\Model\Datasource\Base\BaseDataSource;
use Doctrine\ORM\EntityManagerInterface;
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
                              string $moySkladLogin, string $moySkladPassword,
                              $kernelRootDir, EventDispatcherInterface $dispatcher,
                              SlugifierInterface $sluggable, LoggerInterface $logger)
  {
    parent::__construct($options);
    $this->em = $entityManager;
    
    $this->moySkladLogin = $moySkladLogin;
    $this->moySkladPassword = $moySkladPassword;
    
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
            $errorsMessage .= sprintf('[%s]%s.', $error->code, $error->error). "\n";
          }
        }
  
        $this->logger->error(sprintf('[%s]%s.', $e->getCode(), $errorsMessage));
        throw new \Exception($errorsMessage);
      } catch (\Exception $exception)
      {
        $this->logger->error('Products list not loaded from MoySklad:' . "\n" .  $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString());
        $this->dispatcher->dispatch(
          'aw.sync.order_event.message',
          new GenericEvent('Products list not loaded from MoySklad:' . "\n" . $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString())
        );
        continue;
      }
      
      if ($folderData->name !== 'Сыр в кг') continue;
      
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
      # Наименование, описание, краткое описание и артикул
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
          'is_publication_allowed' => 1,
          'published' => 1,
          'total_stock' => 100,
          'reserved_stock' => 10,
          'is_free_delivery' => 0,
          'rank' => 0.00,
          'sku' => $article,
          'short_description' => $productDb->getShortDescription(),
          'description' => $productDb->getDescription(),
          'package' => 1.000,
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
          'published' => 1,
          'total_stock' => 100,
          'reserved_stock' => 10,
          'is_free_delivery' => 0,
          'rank' => 0.00,
          'package' => 1.000,
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
     
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Product ' . $product->name . ' was loaded from MoySklad.')
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
  
}