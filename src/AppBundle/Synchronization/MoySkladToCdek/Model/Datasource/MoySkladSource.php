<?php

namespace AppBundle\Synchronization\MoySkladToCdek\Model\Datasource;

use Accurateweb\SlugifierBundle\Model\SlugifierInterface;
use Accurateweb\SynchronizationBundle\Model\Datasource\Base\BaseDataSource;
use Doctrine\ORM\EntityManagerInterface;
use MoySklad\Entities\Products\Product;
use MoySklad\Lists\EntityList;
use MoySklad\MoySklad;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class MoySkladSource extends BaseDataSource
{
  private
    $em, $moySkladLogin, $moySkladPassword,
    $kernelRootDir, $dispatcher, $slugifierYandex;
  
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
                              SlugifierInterface $sluggable)
  {
    parent::__construct($options);
    $this->em = $entityManager;
    
    $this->moySkladLogin = $moySkladLogin;
    $this->moySkladPassword = $moySkladPassword;
    
    $this->kernelRootDir = $kernelRootDir;
    $this->dispatcher = $dispatcher;
    
    $this->slugifierYandex = $sluggable;
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
     * @var EntityList $moySkladProducts
     */
    try
    {
      $moySkladProducts = Product::query($sklad)->getList();
    } catch (\Exception $exception)
    {
      #$this->logger->addError('Products list not uploaded from MoySklad:' . "\n" .  $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString());
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
    
    $moySkladProductsAsArray = [];
    
    /**
     * Преобразуем в массив
     *
     * @var Product $product
     */
    foreach ($moySkladProducts->toArray() as $key => $product)
    {
      $now = new \DateTime('now');
      $moySkladProductsAsArray[$key] = [
        'external_code' => $product->code,
        'name' => $product->name,
        'price' => $product->salePrices[0]->value / 100,
        'slug' => $this->slugifierYandex->slugify($product->name),
        'created_at' => $now->format('Y-m-d H:i:s'),
        'is_with_gift' => 0,
        'is_publication_allowed' => 1,
        'published' => 1,
        'total_stock' => 100,
        'reserved_stock' => 0,
        'is_free_delivery' => 0,
        'rank' => 0.00,
      ];

      if (isset($product->article))
      {
        $moySkladProductsAsArray[$key]['sku'] = $product->article;
      }else
      {
        $moySkladProductsAsArray[$key]['sku'] = $moySkladProductsAsArray[$key]['slug'];
      }
      
      if (isset($product->image)) $moySkladProductsAsArray[$key]['image'] = $product->image->meta->href;
      if (isset($product->description))
      {
        # этот костыль необходимо убрать
        # substr что-то непонятное делает со сторкой, после чего из массива не хотит создаваться json
        $short_description = substr($product->description, 0, 50);
        
        $moySkladProductsAsArray[$key]['short_description'] = $product->description; #$short_description;
        $moySkladProductsAsArray[$key]['description'] = $product->description;
        
      }else
      {
        $moySkladProductsAsArray[$key]['description'] = '...';
        $moySkladProductsAsArray[$key]['short_description'] = '...';
      }
      
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Product ' . $product->name . ' uploaded from MoySklad.')
      );
    }
    $productsAsJson = json_encode($moySkladProductsAsArray);
    
    if($productsAsJson === false)
    {
      echo $short_description;
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