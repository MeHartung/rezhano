<?php

namespace StoreBundle\Synchronization\FromMoySklad\Model\Datasource;

use Accurateweb\MoyskladIntegrationBundle\Exception\MoyskladException;
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
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CatalogDataSource extends BaseDataSource
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
    $sklad = MoySklad::getInstance($this->moySkladLogin, $this->moySkladPassword);
    
    /**
     * Все товары с моего склада
     *
     * @var EntityList $moySkladFolders
     */
    try
    {
      $moySkladFolders = ProductFolder::query($sklad)->getList();
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
      $this->logger->error('Products list not loaded from MoySklad:' . "\n" . $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString());
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent('Products list not loaded from MoySklad:' . "\n" . $exception->getMessage() . "\n" . 'Trace: ' . "\n" . $exception->getTraceAsString())
      );
      return null;
    }
    
    if ($moySkladFolders->count() === 0)
    {
      return;
    }
    $this->dispatcher->dispatch(
      'aw.sync.order_event.message',
      new GenericEvent($moySkladFolders->count() . ' folders from MoySklad was loaded')
    );
    $moySkladFoldersAsArray = [];
    
    /**
     * Преобразуем в массив
     *
     * @var ProductFolder $folder
     */
    foreach ($moySkladFolders->toArray() as $key => $folder)
    {
      $parentId = null;
      
      if (strlen($folder->pathName) > 1)
      {
        $parentHref = $folder->relations->productFolder->fields->meta->href;
        
        $parent = $sklad->getClient()->get($parentHref);
        
        if ($parent !== null)
        {
          $parentId = $parent->id;
        }
      }
      
      $moySkladFoldersAsArray[] =
        [
          'guid' => $folder->id,
          'parent_guid' => $parentId,
          'name' => $folder->name
        ];
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent($folder->name . ' was loaded from MoySklad')
      );
    }
    
    $productsAsJson = json_encode($moySkladFoldersAsArray);
    
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