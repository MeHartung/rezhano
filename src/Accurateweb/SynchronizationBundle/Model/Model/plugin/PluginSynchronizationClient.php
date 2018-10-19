<?php

class PluginSynchronizationClient extends BaseSynchronizationClient
{
  public function getAuthKey()
  {
    return $this->checkAuthKey();
  }

  protected function generateAuthKey()
  {
    return md5(uniqid('syncli', true));
  }

  protected function checkAuthKey()
  {
    $authKey = parent::getAuthKey();

    if (!$authKey)
    {
      $authKey = $this->generateAuthKey();
      $this->setAuthKey($authKey);
    }

    return $authKey;
  }

  public function preInsert($con = null)
  {
    $this->checkAuthKey();
    return true;
  }
}