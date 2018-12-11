<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 02.08.17
 * Time: 17:29
 */

namespace StoreBundle\Controller\Menu;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class MenuItemController extends Controller
{
  
  public function headerMenuAction(Request $request)
  {
    $requestUri = $this->get('request_stack')->getParentRequest()->getPathInfo();
    $headerMenuItems = $this->getDoctrine()
      ->getRepository('StoreBundle:Menu\MenuItem')
      ->findBy(array('isHeaderDisplay' => true), array('treeLeft' => 'ASC'));;
    
    return $this->render('StoreBundle:Menu:headerMenu.html.twig', [
        'menuItems' => $headerMenuItems,
        'requestUri' => $requestUri,
        ]
    );
  }

//  public function footerMenuAction()
//  {
//
//    $repository = $this->getDoctrine()->getRepository('StoreBundle:Menu\MenuItem');
//
//    $root = $repository->getRootNode();
//
//    $nodeQb = $repository->childrenQueryBuilder($root);
//    $nodes = $nodeQb->andWhere('node.treeLevel < :maxTreeLevel')
//      ->andWhere('node.isFooterDisplay = 1')
//      ->setParameter('maxTreeLevel', 3)
//      ->getQuery()
//      ->getArrayResult();
//
//    $tree = $repository->buildTreeArray($nodes);
//
//    return $this->render('StoreBundle:Menu:footerMenu.html.twig', array(
//      'nodes' => array_splice($tree, 0, 4),
//      'nodes_rest' => $tree
//    ));
//  }
  
  public function footerMenuAction()
  {
    $headerMenuItems = $this->getDoctrine()
      ->getRepository('StoreBundle:Menu\MenuItem')
      ->findBy(array('isFooterDisplay' => true), array('treeLeft' => 'ASC'));;

    return $this->render('StoreBundle:Menu:footerMenu.html.twig', array(
      'nodes' => $headerMenuItems,
    ));
  }

  public function footerMapsAction()
  {
    $stores = $this->getDoctrine()->getRepository('StoreBundle:Store\Store')->findBy([
      'showFooter' => true,
    ]);
    return $this->render('StoreBundle:Menu:footerMaps.html.twig', [
      'stores' => $stores,
    ]);
  }
}