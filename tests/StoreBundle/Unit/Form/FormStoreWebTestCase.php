<?php

namespace Tests\StoreBundle\Unit\Form;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Tests\StoreBundle\StoreWebTestCase;

abstract class FormStoreWebTestCase extends StoreWebTestCase
{
  /**
   * @var FormFactoryInterface
   */
  protected $factory;
  /**
   * @var FormBuilder
   */
  protected $builder;
  /**
   * @var EventDispatcherInterface|MockObject
   */
  protected $dispatcher;


  protected function setUp ()
  {
    parent::setUp();
    $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
    $this->factory = $this->getClient()->getContainer()->get('form.factory');
    $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
  }

  protected function getExtensions ()
  {
    return array();
  }

  protected function getTypeExtensions ()
  {
    return array();
  }

  protected function getTypes ()
  {
    return array();
  }

  protected function getTypeGuessers ()
  {
    return array();
  }
}