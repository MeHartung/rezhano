<?php

namespace StoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 17.10.2017
 * Time: 13:13
 */
class ImportProductDescriptionTabsCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      // the name of the command (the part after "bin/console")
      ->setName('product:import-description-tabs')

      // the short description shown while running "php bin/console list"
      ->setDescription('Imports product description tabs from Jooouuuooomla.')

      // the full command description shown when running the command with
      // the "--help" option
      ->setHelp("Imports product description tabs from Jooouuuooomla");
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $fieldMap = [
      'description' => ['видеорегистратор', 'обзор', 'описание', 'особенности', 'особенности модели', 'применямость',
        'радар', 'режимы работы/количество камер', 'сертификат', 'спецификация', 'функционал'],
      'attributes_html' => ['характеристики', 'дополнительные опции', 'основные характеристики', 'размеры и вес',
'технические зарактеристики', 'технические характеристики', 'характеристики', 'характеристки'],
      'equipment_html' => ['комплект поставки', 'комплектация'],
      'video_html' => ['видео']
    ];


    $em = $this->getContainer()->get('doctrine')->getManager();

    $connection = $em->getConnection();

    $productStmt = $connection->prepare('
SELECT p.id as `id`, vm_p.product_desc as `description`
FROM products p
INNER JOIN iumvy_virtuemart_products_ru_ru vm_p ON p.virtuemart_product_id = vm_p.virtuemart_product_id');

    $productStmt->execute();
    while ($row = $productStmt->fetch(\PDO::FETCH_ASSOC))
    {
      $tabs = array();

      $matches = array();

      preg_match_all('/{tab\s+([^}]+?)\s*}(.+?)(?=({tab|{\/tabs}))/us', $row['description'], $matches, PREG_SET_ORDER);

      foreach ($matches as $match)
      {
        $tabName = mb_convert_case(strip_tags(trim($match[1])), MB_CASE_LOWER, 'UTF-8');

        foreach ($fieldMap as $name => $values)
        {
          if (in_array($tabName, $values))
          {
            if (!isset($tabs[$name]))
            {
              $tabs[$name] = '';
            }
            else
            {
              $tabs[$name] .= '<p>&nbsp;</p>';
            }

            $tabs[$name] .= $match[2];
          }
        }
      }

      if (!empty($tabs))
      {
        if (!isset($tabs['description']))
        {
          $tabs['description'] = '';
        }

        $fieldsToUpdate = [];
        foreach (array_keys($tabs) as $fieldName)
        {
          $fieldsToUpdate[] = sprintf('%1$s = :%1$s', $fieldName);
        }

        $sql = sprintf('UPDATE products SET %s WHERE id = :id', implode(', ', $fieldsToUpdate));

        $updateStmt = $connection->prepare($sql);

        $updateStmt->bindValue(':id', $row['id']);
        foreach ($tabs as $key => $value)
        {
          $updateStmt->bindValue(':'.$key, $value);
        }
        $updateStmt->execute();
      }
    }

    $productStmt->closeCursor();
  }
}