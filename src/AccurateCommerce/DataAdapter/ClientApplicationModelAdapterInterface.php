<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\DataAdapter;

interface ClientApplicationModelAdapterInterface
{
  function getClientModelName();
  function getClientModelValues($context = null);
  function getClientModelId();
}