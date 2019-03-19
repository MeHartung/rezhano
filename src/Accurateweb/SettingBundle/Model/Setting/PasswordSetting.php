<?php


namespace Accurateweb\SettingBundle\Model\Setting;


use Accurateweb\SettingBundle\Model\Storage\SettingStorageInterface;

class PasswordSetting implements SettingInterface
{
  protected $name;
  protected $description;
  protected $default;
  protected $settingStorage, $encoderKey;
  
  public function __construct(SettingStorageInterface $settingStorage, $name, $description, $default, $encoderKey)
  {
    $this->settingStorage = $settingStorage;
    $this->name = $name;
    $this->description = $description;
    $this->default = $default;
    $this->encoderKey = $encoderKey;
  }
  
  public function getName()
  {
    return $this->name;
  }
  
  public function getValue()
  {
    $value = $this->settingStorage->get($this->name);
    
    if (is_null($value))
    {
      return '';
    }
    
    return (string) $this->decrypt($value);
  }
  
  public function setValue($value)
  {
    $this->settingStorage->set($this->name, (string) $this->encrypt($value));
  }
  
  public function getFormType()
  {
    return 'Symfony\Component\Form\Extension\Core\Type\PasswordType';
  }
  
  public function getFormOptions()
  {
    return array();
  }
  
  public function getStringValue()
  {
    # нельзя вовзращать пароль
    return '************';
  }
  
  public function getDescription()
  {
    return $this->description;
  }
  
  public function getModelTransformer()
  {
    return null;
  }
  
  public function encrypt($encrypt)
  {
    $encrypt = serialize($encrypt);
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
    $key = $this->encoderKey;
    #$key = pack('H*', $this->encoderKey);
    $mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
    $passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
    $encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
    
    return $encoded;
  }
  
  public function decrypt($decrypt)
  {
    $decrypt = explode('|', $decrypt.'|');
    $decoded = base64_decode($decrypt[0]);
    $iv = base64_decode($decrypt[1]);
    if(strlen($iv)!==mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)){ return false; }
    $key = $this->encoderKey;
    $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
    $mac = substr($decrypted, -64);
    $decrypted = substr($decrypted, 0, -64);
    $calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
    if($calcmac!==$mac){ return false; }
    $decrypted = unserialize($decrypted);
    return $decrypted;
  }
  
}