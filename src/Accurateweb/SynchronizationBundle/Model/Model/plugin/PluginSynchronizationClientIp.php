<?php

class PluginSynchronizationClientIp extends BaseSynchronizationClientIp
{
  const VER_4 = 4;
  const VER_6 = 6;

  public function __toString()
  {
    $result = parent::__toString();

    $version = $this->getIpVersion();;
    switch ($version)
    {
      case self::VER_4:
        {
          $result = long2ip($this->getIpv4());;
          break;
        }
    }

    return $result;
  }

  public function getAddress()
  {
    $address = null;

    $ipVersion = $this->getIpVersion();
    switch ($ipVersion)
    {
      case self::VER_4:
        {
          $address = $this->getIpv4();
          break;

        }
      default:
        {
          throw new sfException(sprintf('Unsupported IP version: "%s"', $ipVersion));
        }
    }

    return $address;
  }

  public function getIpv6()
  {
    throw new sfException('IPv6 addresses are not supported at the moment');
  }

  public function setIpv6()
  {
    throw new sfException('IPv6 addresses are not supported at the moment');
  }

  public function setIpVersion($v)
  {
    if (!in_array((int)$v, array(self::VER_4)))
    {
      throw new InvalidArgumentException('Only IPv4 addresses supported');
    }

    parent::setIpVersion($v);
  }
}
