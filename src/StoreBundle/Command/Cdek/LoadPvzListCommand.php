<?php
/*
 * Автор Денис Н. Рагозин <dragozin at accurateweb.ru>
 */
namespace StoreBundle\Command\Cdek;

use Doctrine\ORM\EntityManager;
use AccurateCommerce\Interop\DoctrineAdapter;

use AccurateCommerce\Component\CdekShipping\Api\CdekApiClient;
use StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Description of LoadPvzList
 *
 * @author Денис Н. Рагозин <dragozin at accurateweb.ru>
 */
class LoadPvzListCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
        // the name of the command (the part after "bin/console")
        ->setName('cdek:load-pvzlist')

        // the short description shown while running "php bin/console list"
        ->setDescription('Downloads CDEK pickup point list.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp("Downloads CDEK pickup point list");
  }
  
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $em = $this->getContainer()->get('doctrine')->getManager();

    $cdekApi = $this->getContainer()->get('accuratecommerce.shipping.service.cdek')->getApiClient();

    $pvzList = $cdekApi->getPvzList(null, null, CdekApiClient::PVZTYPE_ALL);

    $output->writeln(sprintf('%d records found in API response', count($pvzList)));

    if (!empty($pvzList))
    {
      /* @var $em EntityManager */
      $output->writeln('Deleting all records from raw pvz list');

      $q = $em->createQuery('DELETE FROM StoreBundle\\Entity\\Store\\Logistics\\Delivery\\Cdek\\CdekRawPvzlist');
      $q->execute();

      $i = 0;
      foreach ($pvzList as $pvzListItem)
      {
        $em->persist($pvzListItem);
        if ($i++ % 1000 == 0)
        {
          $em->flush();
          $em->clear();
        }
      }

      $em->flush();
      $em->clear();

      //Удаляем лишние записи
      $cityListDeleteStatement = $em->getConnection()->prepare(<<<EOF
DELETE `cdek_cities` FROM `cdek_cities`
LEFT JOIN `cdek_pvzlist` ON `cdek_cities`.`code` = `cdek_pvzlist`.`city_code`
WHERE `cdek_pvzlist`.`city_code` IS NULL;
EOF
      );
      $cityListDeleteStatement->execute();
      $nbAffectedRows = $cityListDeleteStatement->rowCount();

      $output->writeln(sprintf('Removed %d missing cities', $nbAffectedRows));

      $cityListInsertStatement = $em->getConnection()->prepare(<<<EOF
INSERT INTO `cdek_cities` (`code`, `name`) 
  SELECT t.`city_code`, t.`city_name`
  FROM `cdek_pvzlist` t
  LEFT JOIN `cdek_cities` o ON t.`city_code` = o.`code`
  WHERE o.`code` IS NULL
  GROUP BY t.`city_code`;
EOF
      );

      $cityListInsertStatement->execute();
      $nbAffectedRows = $cityListInsertStatement->rowCount();

      $output->writeln(sprintf('Inserted %d new cities', $nbAffectedRows));

      $repo = $em->getRepository('StoreBundle:Store\Logistics\Delivery\Cdek\CdekCity');
      /* @var $repo \AccurateCommerce\Entity\Repository\CdekCityRepository */
      $citiesWithoutRegions = $repo
        ->createQueryBuilder('c')
        ->select('c')
        ->where('c.region IS NULL')
        ->getQuery()
        ->getResult();

      $output->writeln('Updating regions');

      foreach ($citiesWithoutRegions as $city)
      {
        /* @var $city CdekCity */
        $regionName = $repo->getRegionNameFromCsv($city);
        if ($regionName)
        {
          $city->setRegion($regionName);
          $em->persist($city);
          $em->flush();
        }
      }
    }

    $output->writeln('Complete.');
  }
  
  
}
