<?php

namespace Accurateweb\SynchronizationBundle\Model\Schema;

use Accurateweb\SynchronizationBundle\Model\Schema\Base\BaseSchema;
use Accurateweb\SynchronizationBundle\Model\Schema\Base\BaseSchemaColumn;

class CommonSchema extends BaseSchema
{

  public function __construct($options = array())
  {
    parent::__construct($options);
    $this->loadColumns();
  }

  protected function configure($options)
  {
    $this->addRequiredOption('columns');
  }

  protected function loadColumns()
  {
    $columns = array();
    $columnOptions = $this->getOption('columns');
    foreach ($columnOptions as $name => $value)
    {
      $value['name'] = $name;
      $columns[$name] = BaseSchemaColumn::fromArray($value);
    }

    $this->setColumns($columns);
  }

}
