<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 17.05.2018
 * Time: 22:56
 */

namespace StoreBundle\Repository\Store\Catalog\Taxonomy;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Gedmo\Exception\UnexpectedValueException;
use Gedmo\Exception\InvalidArgumentException;
use Gedmo\Tool\Wrapper\AbstractWrapper;


class ExtendedNestedTreeRepository extends NestedTreeRepository
{
    /**
     * moves node as next sibling of dest node
     *
     * @param object    $node          target node which has to be shifted.
     * @param object    $sibling       previous sibling of target node.
     * @param object    $parent        new parent of target node.
     *
     * @return boolean TRUE if all queries were successful.
     *
     * @param $node
     * @param $sibling
     * @param $parent
     * @return bool
     * @throws \Exception
     */
    public function moveAsNextSiblingOf($node, $sibling, $parent)
    {
        if (empty($node)) {
            throw new InvalidArgumentException('The target node need to be specified.');
        }
        if (empty($sibling)) {
            throw new InvalidArgumentException('The sibling need to be specified.');
        }
        if (empty($parent)) {
            throw new InvalidArgumentException('The parent need to be specified.');
        }
        if ($node === $sibling) {
            throw new UnexpectedValueException("Cannot move node as next sibling of itself");
        }
        return $this->moveExistingNode(
            $node,
            $node->getRoot()->getId(),
            $sibling->getTreeRight() + 1,
            $sibling->getTreeLevel() - $node->getTreeLevel(),
            $parent->getId()
        );
    }

    /**
     * moves node as previous sibling of dest node
     *
     * @param object    $node          target node which has to be shifted.
     * @param object    $sibling       next sibling of target node.
     * @param object    $parent        new parent of target node.
     *
     * @return boolean TRUE if all queries were successful.
     * @throws \Exception
     */
    public function moveAsPrevSiblingOf($node, $sibling, $parent)
    {
        if (empty($node)) {
            throw new InvalidArgumentException('The target node need to be specified.');
        }
        if (empty($sibling)) {
            throw new InvalidArgumentException('The sibling need to be specified.');
        }
        if (empty($parent)) {
            throw new InvalidArgumentException('The parent need to be specified.');
        }
        if ($node === $sibling) {
            throw new UnexpectedValueException("Cannot move node as previous sibling of itself");
        }
        return $this->moveExistingNode(
            $node,
            $node->getRoot()->getId(),
            $sibling->getTreeLeft(),
            $sibling->getTreeLevel() - $node->getTreeLevel(),
            $parent->getId()
        );
    }

    /**
     * move node's and its children to location $destLeft and updates rest of tree
     *
     * @param object    $node         target node which has to be shifted.
     * @param int       $rootId       id of the root node
     * @param int       $destLeft     destination left value
     * @param int       $levelDiff    difference of the level to shift
     * @param int       $newParentId  id of the new parent node
     *
     * @return boolean TRUE if all queries were successful.
     */
    private function moveExistingNode(
        $node = null,
        int $rootId = null,
        int $destLeft = null,
        int $levelDiff = null,
        int $newParentId = null
    ) {
        if (empty($node)) {
            throw new InvalidArgumentException('The target node need to be specified.');
        }
        if ($rootId === null) {
            throw new InvalidArgumentException('The id of the tree root need to be specified.');
        }
        if ($destLeft === null) {
            throw new InvalidArgumentException('The left value of destination need to be specified.');
        }
        if ($levelDiff === null) {
            throw new InvalidArgumentException('The difference of the level need to be specified.');
        }
        if ($newParentId === null) {
            throw new InvalidArgumentException('The id of the new parent need to be specified.');
        }

        $em              = $this->getEntityManager();
        $wrapped         = AbstractWrapper::wrap($node, $em);
        $meta            = $wrapped->getMetadata();
        $config          = $this->listener->getConfiguration($em, $meta->name);
        $identifierField = $meta->getSingleIdentifierFieldName();
        $nodeId          = $wrapped->getIdentifier();
        $left            = $wrapped->getPropertyValue($config['left']);
        $right           = $wrapped->getPropertyValue($config['right']);
        $treeSize        = $right - $left + 1;
        if ($left >= $destLeft) { // src was shifted too?
            $left       += $treeSize;
            $right      += $treeSize;
        }
        $delta           = $destLeft - $left;

        $em->beginTransaction();
        try {
            // Make room in the new branch
            $this->listener
                ->getStrategy($em, $meta->name)
                ->shiftRL($em, $config['useObjectClass'], $destLeft, $treeSize, $rootId);

            // now there's enough room next to target to move the subtree
            $this->listener
                ->getStrategy($em, $meta->name)
                ->shiftRangeRL($em, $config['useObjectClass'], $left, $right, $delta, $rootId, $rootId, $levelDiff);

            // correct values after source (close gap in old tree)
            $this->listener
                ->getStrategy($em, $meta->name)
                ->shiftRL($em, $config['useObjectClass'], $right + 1, -$treeSize, $rootId);

            // point to new parent
            $qb = $em->createQueryBuilder();
            $qb->update($config['useObjectClass'], 'node')
                ->set('node.' . $config['parent'], ':pid')
                ->setParameter('pid', $newParentId)
                ->where($qb->expr()->eq('node.' . $identifierField, ':id'))
                ->setParameter('id', $nodeId);
            $qb->getQuery()->getSingleScalarResult();

            $em->commit();

            return true;
        } catch (\Exception $ex) {
            $em->rollback();
            $em->close();
            throw $ex;
        }
    }
}