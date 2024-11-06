<?php

namespace Accurateweb\MoyskladIntegrationBundle\Exception;

use MoySklad\Exceptions\ApiResponseException;

class MoyskladExceptionFactory
{
  const UNIQUE_FIELD_EXCEPTION = 3006;

  public static function throwException(ApiResponseException $exception)
  {
    $code = $exception->getApiCode();

    switch ($code)
    {
      case self::UNIQUE_FIELD_EXCEPTION:
        return new MoyskladUniqueFieldException($exception->getErrorText(), $exception->getApiCode(), $exception->getMoreInfo());
      default:
        return new MoyskladException($exception->getErrorText(), $exception->getApiCode(), $exception->getMoreInfo());
    }
  }
}