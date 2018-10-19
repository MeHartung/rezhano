<?php

/*
 * Автор Денис Н. Рагозин <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Interop;

use AppKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Description of SymfonyProxy
 *
 * @author Денис Н. Рагозин <dragozin at accurateweb.ru>
 */
class SymfonyProxy
{
  private $kernel,
          $request,
          $response;
  
  private static $instance = null;
  
  public function __construct()
  {
    $kernel = new AppKernel('dev', false);
    $kernel->loadClassCache();    
    
    $this->kernel = $kernel;
    
    Request::enableHttpMethodParameterOverride();
    $this->request = Request::createFromGlobals();
    
    try
    {
      $this->response = $kernel->handle($this->request);
    }
    catch (NotFoundHttpException $ex) 
    {
      //Это ок, так как мы на самом деле ничего не обрабатываем. Нам нужно просто чтобы отрендерился фрагмент
    }
  }
  
  /**
   * @return SymfonyProxy
   */
  static public function createInstance()
  {
    self::$instance = new SymfonyProxy();
    
    return self::$instance;
  }
  
  /**
   * 
   * @return SymfonyProxy
   */
  static public function getInstance()
  {
    return self::$instance;
  }
  
  /**
   * 
   * @return \Symfony\Component\HttpKernel\Kernel
   */
  public function getSymfonyKernel()
  {
    return $this->kernel;
  }
  
  public function __destruct()
  {
    $this->kernel->terminate($this->request, $this->response);
  }
  
  /**
   * @return Request
   */
  public function getRequest()
  {
    return $this->request;
  }
}
