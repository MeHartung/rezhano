<?php

namespace Tests\StoreBundle\Admin;


use Tests\StoreBundle\StoreWebTestCase;

class AdminStoreWebTestCase extends StoreWebTestCase
{
  protected function setUp ()
  {
    parent::setUp();
    $this->logIn();
  }
}