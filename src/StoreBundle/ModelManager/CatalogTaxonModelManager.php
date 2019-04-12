<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 21.02.18
 * Time: 14:41
 */

namespace StoreBundle\ModelManager;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\PessimisticLockException;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Exception\InvalidOperationException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Monolog\Logger;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CatalogTaxonModelManager extends NestedSetModelManager
{
    private $logger;

    public function __construct(RegistryInterface $registry, Logger $logger)
    {
        $this->logger = $logger;
        parent::__construct($registry, $logger);
    }

    /**
  * @param $object Taxon
  * @throws DBALException
  * @throws InvalidOperationException
  * @throws ModelManagerException
  * @throws \Doctrine\DBAL\ConnectionException
  */
  public function create($object)
  {
    $entityManager = $this->getEntityManager($object);

   /** User can't edit Root */
    if(!$object->getParent()){ throw new InvalidOperationException('Невозможно создать корневой раздел'); }

    $conn = $entityManager->getConnection();

    try
    {
//      $conn->setAutoCommit(false);
//      $conn->setTransactionIsolation(1);
      $entityManager->persist($object);
//      $conn->beginTransaction(); # для того, чтобы не было START TRANSACTION после Lock tables

//      $tableName = $entityManager->getClassMetadata(get_class($object))->getTableName();
//      $lockTableSql = sprintf("LOCK TABLES %s WRITE", $tableName);
//      $conn->query($lockTableSql)->execute(); # залочили табл. на запись

      $entityManager->flush();

//      $conn->commit();
    }
    catch (\PDOException $e)
    {
       $conn->rollBack();
       $this->logger->addError("New node create exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());

       throw new ModelManagerException(sprintf('Failed to create object: %s', ClassUtils::getClass($object)),
                                       $e->getCode(), $e);
    }
    catch (DBALException $e)
    {
      $conn->rollBack();

      $this->logger->addError("New node create exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());

      throw new ModelManagerException(sprintf('Failed to create object: %s', ClassUtils::getClass($object)),
                                      $e->getCode(), $e );
    }
    catch (\Exception $e)
    {
        $conn->rollBack();
        $this->logger->addError("New node create exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());

        throw new ModelManagerException(sprintf("Failed to create object: %s. \n %s",get_class($object),
                                        $e->getMessage()),$e->getCode(), $e);
    }
//    finally
//    {
//        $conn->query("UNLOCK TABLES")->execute();
//    }
  }

  /**
  * @param $object Taxon
  * @throws DBALException
  * @throws InvalidOperationException
  * @throws ModelManagerException
  * @throws \Doctrine\DBAL\ConnectionException
  */
  public function update($object)
  {
      $entityManager = $this->getEntityManager($object);

      /** User can't edit Root */
      if(!$object->getParent()){ throw new InvalidOperationException('Невозможно изменить корневой раздел'); }

      $conn = $entityManager->getConnection();

      try
      {
          $conn->setAutoCommit(false);

          $entityManager->persist($object);
          $conn->beginTransaction(); # для того, чтобы не было START TRANSACTION после Lock tables

//          $tableName = $entityManager->getClassMetadata(get_class($object))->getTableName();
//          $lockTableSql = sprintf("LOCK TABLES %s WRITE", $tableName);
//          $conn->query($lockTableSql)->execute(); # залочили табл. на запись

          $entityManager->flush();

          $conn->commit();
      }
      catch (\PDOException $e)
      {
          $conn->rollBack();
          $this->logger->addError("New node create exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());

          throw new ModelManagerException(sprintf('Failed to create object: %s', ClassUtils::getClass($object)),
              $e->getCode(), $e);
      }
      catch (DBALException $e)
      {
          $conn->rollBack();

          $this->logger->addError("New node create exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());

          throw new ModelManagerException(sprintf('Failed to create object: %s', ClassUtils::getClass($object)),
              $e->getCode(), $e );
      }
      catch (\Exception $e)
      {
          $conn->rollBack();

          throw new ModelManagerException(sprintf("Failed to create object: %s. \n %s",get_class($object),
              $e->getMessage()),$e->getCode(), $e);
      }
//      finally
//      {
//          $conn->query("UNLOCK TABLES")->execute();
//      }
  }

    /**
     * @param $object
     * @throws InvalidOperationException
     * @throws ModelManagerException
     * @throws PessimisticLockException
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
  public function delete($object)
  {
      $entityManager = $this->getEntityManager($object);

      /** User can't edit Root */
      if(!$object->getParent()){ throw new InvalidOperationException('Невозможно изменить корневой раздел'); }

      $conn = $entityManager->getConnection();
      try
      {
          $conn->setAutoCommit(false);

          $entityManager->remove($object);
          /** @var NestedTreeRepository $repo */
          $repo = $entityManager->getRepository(get_class($object));
          $conn->beginTransaction(); # для того, чтобы не было START TRANSACTION после Lock tables

//          $tableName = $entityManager->getClassMetadata(get_class($object))->getTableName();
//          $lockTableSql = sprintf("LOCK TABLES %s WRITE", $tableName);
//          $conn->query($lockTableSql)->execute(); # залочили табл. на запись

          $repo->removeFromTree($object);

          $conn->commit();
      }
      catch (\PDOException $e)
      {
          $conn->rollBack();
          $this->logger->addError("New node create exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());

          throw new ModelManagerException(sprintf('Failed to create object: %s', ClassUtils::getClass($object)),
              $e->getCode(), $e);
      }
      catch (DBALException $e)
      {
          $conn->rollBack();

          $this->logger->addError("New node create exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());

          throw new ModelManagerException(sprintf('Failed to create object: %s', ClassUtils::getClass($object)),
              $e->getCode(), $e );
      }
      catch (\Exception $e)
      {
          $conn->rollBack();

          throw new ModelManagerException(sprintf("Failed to create object: %s. \n %s",get_class($object),
              $e->getMessage()),$e->getCode(), $e);
      }
      finally
      {
//          $conn->query("UNLOCK TABLES")->execute();
      }
  }
}