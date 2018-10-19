<?php

namespace StoreBundle\Controller\Admin\TreeAdmin;

use Doctrine\ORM\EntityManager;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Repository\Store\Catalog\Taxonomy\ExtendedNestedTreeRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TreeAdminController extends CRUDController
{
    public function listAction()
    {
        $request = $this->getRequest();
        if ($listMode = $request->get('_list_mode')) {
            $this->admin->setListMode($listMode);
        }
        $listMode = $this->admin->getListMode();

        if ($listMode === 'tree') {
            $this->admin->checkAccess('list');

            $preResponse = $this->preList($request);
            if ($preResponse !== null) {
                return $preResponse;
            }

            return $this->render(
                'RedCodeTreeBundle:CRUD:tree.html.twig',
                [
                    'action' => 'list',
                    'csrf_token' => $this->getCsrfToken('sonata.batch'),
                    '_sonata_admin' => $request->get('_sonata_admin'),
                ],
                null,
                $request
            );
        }

        return parent::listAction();
    }

    /**
     * @return JsonResponse
     * @throws \Sonata\AdminBundle\Exception\ModelManagerException
     */
    public function treeDataAction()
    {
        $request = $this->getRequest();

        $doctrine = $this->get('doctrine');
        /** @var EntityManager $em */
        $em = $doctrine->getManagerForClass($this->admin->getClass());
        /** @var NestedTreeRepository $repo */
        $repo = $em->getRepository($this->admin->getClass());

        $operation = $request->get('operation');
        switch ($operation) {
            case 'get_node':
                $nodeId = $request->get('id');
                if ($nodeId) {
                    $parentNode = $repo->find($nodeId);
                    $nodes = $repo->getChildren($parentNode, true);
                } else {
                    $nodes = $repo->getRootNodes();
                }

                $nodes = array_map(
                    function ($node) {
                        return [
                            'id' => $node->getId(),
                            'text' => (string)$node,
                            'children' => true,
                        ];
                    },
                    $nodes
                );

                return new JsonResponse($nodes);
            case 'move_node':
                $nodeId = $request->get('id');
                $parentNodeId = $request->get('parent_id');
                $em->clear();
                $repo->clear();
                $parentNode = $repo->find($parentNodeId);
                /** @var Taxon $node */
                $node = $repo->find($nodeId);
                $position = $request->get("position");

                $siblings = $parentNode->getChildren();

                $sibling = isset($siblings[$position-1]) && count($siblings ) > 0 ? $siblings[$position-1] : null;

                try {
                    #   нет детей у будущего родителя
                    if(($parentNode->getId() !== $node->getParent()->getId()) && ($position == 0 || !$position) || !$siblings){
                        $node->setParent($parentNode);
                        $this->admin->getModelManager()->update($node);
                    }
                    #  если мы перемещаем не на первую позицию, но не меняем родителя
                    elseif (($parentNode->getId() == $node->getParent()->getId() )&& ((int)$position !== 0)) {
                        $this->admin->getModelManager()->moveNodeToNotFirstPosition($node, $sibling, $parentNode);
                    } # если позиция нулевая и родитель тот же
                    elseif(($parentNode->getId() == $node->getParent()->getId()) && ($position == 0 || !$position))
                    {
                        $this->admin->getModelManager()->moveAsPrevSiblingOfBranch($node, $siblings[0], $parentNode);
                    }
                    # если мы таким меняем родителя
                    else{
                        if($position == 0 || !$position){
                            $node->setParent($parentNode);
                            $this->admin->getModelManager()->update($node);
                        }else
                        {
                            $this->admin->getModelManager()->moveNodeToNotFirstPosition($node, $sibling, $parentNode);
                        }
                    }

                    $response = new JsonResponse(['id' => $node->getId(),
                        'text' => $node->{'get' . ucfirst($this->admin->getTreeTextField())}(),], 200);

                } catch (\Exception $exception) {
                    $this->getLogger()->error($exception->getTraceAsString());
                    $response = new JsonResponse(["error" => "Ошибка перемещения"], 400);
                }

                return $response;
            case 'rename_node':
                $nodeId = $request->get('id');
                $nodeText = $request->get('text');
                $node = $repo->find($nodeId);

                $node->{'set' . ucfirst($this->admin->getTreeTextField())}($nodeText);
                $this->admin->getModelManager()->update($node);

                return new JsonResponse(
                    [
                        'id' => $node->getId(),
                        'text' => $node->{'get' . ucfirst($this->admin->getTreeTextField())}(),
                    ]
                );
            case 'create_node':
                $parentNodeId = $request->get('parent_id');
                $parentNode = $repo->find($parentNodeId);
                $nodeText = $request->get('text');
                $node = $this->admin->getNewInstance();
                $node->{'set' . ucfirst($this->admin->getTreeTextField())}($nodeText);
                $node->setParent($parentNode);
                $this->admin->getModelManager()->create($node);

                return new JsonResponse(
                    [
                        'id' => $node->getId(),
                        'text' => $node->{'get' . ucfirst($this->admin->getTreeTextField())}(),
                    ]
                );
            case 'delete_node':
                $nodeId = $request->get('id');
                $node = $repo->find($nodeId);
                $this->admin->getModelManager()->delete($node);

                return new JsonResponse();
        }

        throw new BadRequestHttpException('Unknown action for tree');
    }
}