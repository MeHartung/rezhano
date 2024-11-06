<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 12.09.17
 * Time: 15:30
 */

namespace StoreBundle\Controller\Admin\Product;


use StoreBundle\Jstree\CatalogSectionTreeListItemClientModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ProductTaxonomyAdminController extends Controller
{

  public function indexAction( $value = null, $attributes = array(), $errors = array())
  {

    $name = '';
/*    $id = $this->generateId($name);*/
  $id = 'product_taxons';

    if (null !== $value)
    {
      if (!is_array($value))
      {
        $values = array($value);
      }
      else
      {
        $values = $value;
      }
    }
    else
    {
      $values = array();
    }
    $em = $this->getDoctrine()->getManager();

    $rootSection =$em
      ->getRepository('StoreBundle:Store\Catalog\Taxonomy\Taxon')
      ->getRootNode()
    ;

    $rootClientModel = new CatalogSectionTreeListItemClientModel($rootSection);

    $jsTreeData = $this->selectNodes(array($rootClientModel->getClientModelValues()), $values);
    $jsTreeData = $jsTreeData[0];

    $html ='';
     $html .= sprintf(<<<EOF
<script type="text/javascript">    
        (function($){
$(function(){
  $('#%s').jstree_choice({
    json_data: %s,
    name: "%s",
    multiple: %s
  })          
}) })(jQuery)
</script>            
EOF
       , $id, json_encode($jsTreeData['children']), $name, (bool)true/*$this->getOption('multiple')*/);

    return $this->render('StoreBundle:Catalog\Product:product_taxon.html.twig', [ 'html' => $html]);
  }

  public function selectNodes($jsTreeData, $nodeIds)
  {
    foreach ($jsTreeData[0] as $idx => $node)
    {
      if (isset($node['metadata']['id']) && in_array((string)$node['metadata']['id'], $nodeIds))
      {
        if (!isset($jsTreeData[$idx]['attr']))
        {
          $jsTreeData[$idx]['attr'] = array();
        }
        $jsTreeData[$idx]['attr']['class'] = 'jstree-checked';
      }
      if (isset($node['children']))
      {
        $jsTreeData[$idx]['children'] = $this->selectNodes($node['children'], $nodeIds);
      }
    }
    return $jsTreeData;
  }
}