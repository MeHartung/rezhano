<?php


namespace StoreBundle\ModelManager;


use Doctrine\Common\Util\ClassUtils;
use Psr\Log\LoggerInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Repository\Store\Catalog\Taxonomy\ExtendedNestedTreeRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class NestedSetModelManager extends ModelManager
{
  private $logger;

  public function __construct (RegistryInterface $registry, LoggerInterface $logger)
  {
    $this->logger = $logger;
    parent::__construct($registry);
  }

  /**
   * @param $node       Taxon
   * @param $parentNode Taxon
   * @param $sibling    Taxon
   * @throws \Exception
   */
  public function moveNodeToNotFirstPosition ($node, $sibling, $parentNode)
  {
    $entityManager = $this->getEntityManager(get_class($node));
    $extendedNestedTreeRepo = new ExtendedNestedTreeRepository($entityManager,
      $entityManager->getClassMetadata(get_class($node)));

    $conn = $entityManager->getConnection();

    try
    {
      $conn->setAutoCommit(false);

      $conn->beginTransaction(); # для того, чтобы не было START TRANSACTION после Lock tables

      //          $tableName = $entityManager->getClassMetadata(get_class($node))->getTableName();
      //          $lockTableSql = sprintf("LOCK TABLES %s WRITE", $tableName);
      //          $conn->query($lockTableSql)->execute(); # залочили табл. на запись

      $extendedNestedTreeRepo->moveAsNextSiblingOf($node, $sibling, $parentNode);

      $conn->commit();

    }
    catch (\Exception $exception)
    {
      $conn->rollBack();

      $this->logger->addError("New node move exception: " . $exception->getMessage() . "\n" . $exception->getTraceAsString());

      throw new ModelManagerException(sprintf('Failed to create object: %s', ClassUtils::getClass($node)),
        $exception->getCode(), $exception);

    }
    finally
    {
      //          $conn->query("UNLOCK TABLES")->execute();
    }
  }

  public function moveAsPrevSiblingOfBranch ($node, $sibling, $parentNode)
  {
    $entityManager = $this->getEntityManager(get_class($node));
    $extendedNestedTreeRepo = new ExtendedNestedTreeRepository($entityManager,
      $entityManager->getClassMetadata(get_class($node)));

    $conn = $entityManager->getConnection();

    try
    {
      $conn->setAutoCommit(false);

      $conn->beginTransaction(); # для того, чтобы не было START TRANSACTION после Lock tables

      //          $tableName = $entityManager->getClassMetadata(get_class($node))->getTableName();
      //          $lockTableSql = sprintf("LOCK TABLES %s WRITE", $tableName);
      //          $conn->query($lockTableSql)->execute(); # залочили табл. на запись

      $extendedNestedTreeRepo->moveAsPrevSiblingOf($node, $sibling, $parentNode);

      $conn->commit();

    }
    catch (\Exception $exception)
    {
      $conn->rollBack();

      $this->logger->addError("New node move exception: " . $exception->getMessage() . "\n" . $exception->getTraceAsString());

      throw new ModelManagerException(sprintf('Failed to create object: %s', ClassUtils::getClass($node)),
        $exception->getCode(), $exception);

    }
    finally
    {
      //          $conn->query("UNLOCK TABLES")->execute();
    }
  }
}