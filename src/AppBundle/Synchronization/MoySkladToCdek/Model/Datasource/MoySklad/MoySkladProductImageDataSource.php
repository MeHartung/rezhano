<?php


namespace AppBundle\Synchronization\MoySkladToCdek\Model\Datasource\MoySklad;

use Accurateweb\SlugifierBundle\Model\SlugifierInterface;
use Accurateweb\SynchronizationBundle\Model\Datasource\Base\BaseDataSource;
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
     * Список внешних кодов продуктов, относящихся к моему складу
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
      $codes[] =  $productsCode;
    }
    
    try
    {
      $moySkladProductsImages = Product::query($sklad)->filter($filter);
    } catch (\Exception $exception)
    {
      #$this->logger->addError('Products list not uploaded from MoySklad:' . "\n" .  $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString());
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Products list not uploaded from MoySklad:' . "\n" . $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString())
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
      var_dump($productImage);die;
      $urls = $productImage->image;
      
      $product = $this->em->getRepository('StoreBundle:Store\Catalog\Product\Product')->findOneBy(
        [
          'externalCode' => $productImage->code
        ]
      );
      
      foreach ($urls as $url)
      {
        $path = $this->kernelRootDir . '/../web/uploads/product-photo/' . $product->getId();
        file_put_contents($path, file_get_contents($url));
        $photoUrls[] = [
          'product_id' => $product->getId(),
          'filename' => ''];
      }
      
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Product ' . $productImage->name . ' uploaded from MoySklad.')
      );
    }
    
    $productsAsJson = json_encode($photoUrls);
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