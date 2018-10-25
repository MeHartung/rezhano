<?php


namespace StoreBundle\Synchronization\FromMoySklad\Model\Datasource;

use Accurateweb\SlugifierBundle\Model\SlugifierInterface;
use Accurateweb\SynchronizationBundle\Model\Datasource\Base\BaseDataSource;
use Accurateweb\SynchronizationBundle\Model\Subject\MoySkladImageSubject;
use Doctrine\ORM\EntityManagerInterface;
use MoySklad\Components\FilterQuery;
use MoySklad\Entities\Products\Product;
use MoySklad\Lists\EntityList;
use MoySklad\MoySklad;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class MoySkladProductImageDataSource extends BaseDataSource
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
  public function __construct($options = array(), $to,
                              EntityManagerInterface $entityManager,
                              string $moySkladLogin, string $moySkladPassword,
                              $kernelRootDir, EventDispatcherInterface $dispatcher)
  {
    parent::__construct($options);
    $this->em = $entityManager;
    
    $this->moySkladLogin = $moySkladLogin;
    $this->moySkladPassword = $moySkladPassword;
    
    $this->kernelRootDir = $kernelRootDir;
    $this->dispatcher = $dispatcher;
  }
  
  /**
   *
   * @param $from string
   * @param null $to
   * @return string|null
   */
  public function get($from, $to = null, $em = null)
  {
    $sklad = MoySklad::getInstance($this->moySkladLogin, $this->moySkladPassword);
    
    /**
     * Список внешних кодов продуктов, относящихся к моему складу
     *
     * @var EntityList $moySkladProductsImages
     */
    $productsQb = $this->em->getRepository('StoreBundle:Store\Catalog\Product\Product')->createQueryBuilder('p');
    $productsCodes = $productsQb->select('p.externalCode')
      ->where('p.externalCode IS NOT NULL')
      ->getQuery()->getResult();
    
    $filter = new FilterQuery();
    $codes = [];
    # фильтр работает по типу IN()
    # нельзя сделать выборку по тем, у которых есть фото
    foreach ($productsCodes as $productsCode)
    {
      $filter->eq('code', $productsCode['externalCode']);
      $codes[] = $productsCode;
    }
    
    try
    {
      $moySkladProductsImages = Product::query($sklad)->getList();
    } catch (\Exception $exception)
    {
      #$this->logger->addError('Products list not loaded from MoySklad:' . "\n" .  $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString());
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Products list not loaded from MoySklad:' . "\n" . $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString())
      );
      return null;
    }
    
    if ($moySkladProductsImages->count() === 0)
    {
      return;
    }
    
    $photoUrls = [];
    
    /**
     * Преобразуем в массив
     *
     * @var Product $productImage
     */
    foreach ($moySkladProductsImages->toArray() as $key => $productImage)
    {
      if (!isset($productImage->image)) continue;
      
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Try load image for ' . $productImage->name)
      );
      
      $url = $productImage->image->meta->href;
      
      $product = $this->em->getRepository('StoreBundle:Store\Catalog\Product\Product')->findOneBy(
        [
          'externalCode' => $productImage->code
        ]
      );

      if($product == null) continue;
      
      $varDir = $this->kernelRootDir . '/../var/uploads/';
      $webDir = $this->kernelRootDir . '/../web/uploads/';
      
      $varPathProducts = $varDir.'product-photo/';
      $webPathProducts = $webDir.'product-photo/';
      
      $fileName = $product->getId() . '/' . $productImage->image->filename;
      
      $varMkDirResult = $this->checkDir($varPathProducts);
      $webMkDirResult = $this->checkDir($webPathProducts);
      
      if (!$varMkDirResult || !$webMkDirResult)
      {
        $this->dispatcher->dispatch(
          'aw.sync.order_event.message',
          new GenericEvent("Product with id " . $product->getId() . " was skipped, because can't create folders.")
        );
      }
  
      $varPathProduct = $this->kernelRootDir . '/../var/uploads/product-photo/'.$product->getId().'/';
      $webPathProduct = $this->kernelRootDir . '/../web/uploads/product-photo/'.$product->getId().'/';
  
      $varMkDirResult = $this->checkDir($varPathProduct);
      $webMkDirResult = $this->checkDir($webPathProduct);
  
      if (!$varMkDirResult || !$webMkDirResult)
      {
        $this->dispatcher->dispatch(
          'aw.sync.order_event.message',
          new GenericEvent("Product with id " . $product->getId() . " was skipped, because can't create folders.")
        );
        continue;
      }
      
      $dbPath = 'product-photo/' . $product->getId() . '/' . $productImage->image->filename;
      $imageLoadResult = $this->saveImage($url, $webPathProducts . $fileName, $varPathProducts . $fileName);
      
      if ($imageLoadResult)
      {
        $photoUrls[] = [
          'product_id' => $product->getId(),
          'filename' => $dbPath,
          'position' => 0
        ];
        
        $this->dispatcher->dispatch(
          'aw.sync.order_event.message',
          new GenericEvent("Image for product $productImage->name loaded from MoySklad to ".realpath($webPathProducts.$fileName)." and " . realpath($varPathProducts.$fileName))
        );
      } else
      {
        $this->dispatcher->dispatch(
          'aw.sync.order_event.message',
          new GenericEvent('Can\'t load image from MoySklad for' . $productImage->name)
        );
      }
      
    }
    $productsImagesAsJson = json_encode($photoUrls);
    
    if ($productsImagesAsJson === false)
    {
      throw new \Exception('Не удалось создать json');
    }
    
    file_put_contents($to, $productsImagesAsJson);
    
    return $to;
  }
  
  /**
   * @param $from
   * @param $to
   */
  public function put($from, $to)
  {
  
  }
  
  private function checkDir($path)
  {
    $mkDirResult = TRUE;
    $realpath = realpath($path);

    if($realpath === false)
    {
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Try create folder ' . $path . '...')
      );
  
      $mkDirResult = $this->createDir($path);
    }
    
    return $mkDirResult;
  }
  

  
  /**
   * Пытается создать папку
   * @param $path
   * @return bool
   */
  public function createDir($path)
  {
    $mkDirResult = mkdir($path);
  
    if ($mkDirResult === false)
    {
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Can\'t create folder ' . $path)
      );
    } else
    {
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Ok!')
      );
    }
    
    return $mkDirResult;
  }
  
  /**
   * @param $imageUrl
   * @param $webPath
   * @return bool
   */
  private function saveImage($imageUrl, $webPath, $varPath)
  {
    $result = false;
    
    # тут авторизуемся и получаем прямой url картинки
    $imgData = curl_init($imageUrl);
    curl_setopt($imgData, CURLOPT_HEADER, true);
    curl_setopt($imgData, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($imgData, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($imgData, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($imgData, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($imgData, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($imgData, CURLOPT_USERPWD, $this->moySkladLogin . ':' . $this->moySkladPassword);
    $output = curl_exec($imgData);
    
    if ($output === false)
    {
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Can\'t load image from MoySklad:' . "\n" . curl_error($imgData))
      );
      return false;
    }
    
    $response = curl_getinfo($imgData);
    curl_close($imgData);
    
    if ($response['url'])
    {
      try
      {
        # выкачиваем изображение
        $ch = curl_init($response['url']);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $rawdata = curl_exec($ch);
        curl_close($ch);
      } catch (\Exception $exception)
      {
        $this->dispatcher->dispatch(
          'aw.sync.order_event.message',
          new GenericEvent('Can\'t load image from MoySklad:' . "\n" . $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString())
        );
      }
      
      try
      {
        $fp = fopen($webPath, 'w');
        fwrite($fp, $rawdata);
        fclose($fp);
        $result = true;
      } catch (\Exception $exception)
      {
        $this->dispatcher->dispatch(
          'aw.sync.order_event.message',
          new GenericEvent('Can\'t load save image to ' . $webPath . "\n" . $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString())
        );
        #$this->logger->addError();
      }
      
      try
      {
        $fp = fopen($varPath, 'w');
        fwrite($fp, $rawdata);
        fclose($fp);
        $result = true;
      } catch (\Exception $exception)
      {
        $this->dispatcher->dispatch(
          'aw.sync.order_event.message',
          new GenericEvent('Can\'t load save image to ' . $varPath . "\n" . $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString())
        );
        #$this->logger->addError();
      }
    }
    return $result;
  }
  
}