<?php

namespace Accurateweb\ClientApplicationBundle\Twig;

use AccurateCommerce\DataAdapter\ClientApplicationModelCollection;
use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterManagerInterface;

class ClientApplicationExtension extends \Twig_Extension
{
  private $manager;

  public function __construct (ClientApplicationModelAdapterManagerInterface $manager)
  {
    $this->manager = $manager;
  }

  public function getFilters()
  {
    return array(
      new \Twig_SimpleFilter(
        'client_model',
        array($this, 'getClientModel'),
        array('is_safe' => array('html'))
      ),
      new \Twig_SimpleFilter(
        'client_model_collection',
        array($this, 'getClientModelCollection'),
        array('is_safe' => array('html'))
      )
    );
  }

  /**
   * @param $subject
   * @param $adapter string
   * @param array $options
   * @param null|string $model_name
   * @return string
   * @throws \Accurateweb\ClientApplicationBundle\Exception\NotFoundClientApplicationModelAdapterException
   * @throws \Exception
   */
  public function getClientModel($subject, $adapter, $options=array(), $model_name = null)
  {
    $adapter = $this->manager->getModelAdapter($adapter);

    if (!$adapter->supports($subject))
    {
      throw new \Exception(sprintf('Adapter not support %s', get_class($subject)));
    }

    $script = sprintf(<<<EOF
<script type="text/javascript">
    if ('undefined' === typeof ObjectCache) {
      ObjectCache = {};
    }
    ObjectCache['%s'] = %s;
</script>  
EOF
,
      is_null($model_name)?$adapter->getModelName():$model_name,
    json_encode($adapter->transform($subject, $options))
    );

    return $script;
  }

  /**
   * @param $subjects array
   * @param $adapter string
   * @param $collectionName string
   * @param array $options
   * @return string
   * @throws \Accurateweb\ClientApplicationBundle\Exception\NotFoundClientApplicationModelAdapterException
   * @throws \Exception
   */
  public function getClientModelCollection($subjects, $adapter, $collectionName, $options=array())
  {
    $adapter = $this->manager->getModelAdapter($adapter);
    $data = array();

    foreach ($subjects as $subject)
    {
      if (!$adapter->supports($subject))
      {
        throw new \Exception(sprintf('Adapter not support %s', get_class($subject)));
      }

      $data[] = $adapter->transform($subject, $options);
    }


    $script = sprintf(<<<EOF
<script type="text/javascript">
    if ('undefined' === typeof ObjectCache) {
      ObjectCache = {};
    }
    ObjectCache['%s'] = %s;
</script>  
EOF
      ,
      $collectionName,
      json_encode($data)
    );

    return $script;
  }
}