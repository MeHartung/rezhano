<?php

namespace StoreBundle\Controller\Text;

use StoreBundle\DataAdapter\Text\CheeseStoryAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CheeseStoryController extends Controller
{
  /**
   * @param Request $request
   */
  public function indexAction(Request $request)
  {
    $stories = $this->getDoctrine()->getRepository('StoreBundle:Text\CheeseStory')->findBy([], [
      'position' => 'ASC'
    ]);
    
    if ($request->isXmlHttpRequest())
    {
      $result = [];
      
      if (count($stories) > 0)
      {
        $adapter = new CheeseStoryAdapter($stories[0]);
        foreach ($stories as $cheeseStory)
        {
          $result[] = $adapter->transform($cheeseStory);
        }
      }
      
      return new JsonResponse($result, 200);
    }
    
    return $this->render('@App/CheeseStory/index.html.twig', [
      'stories' => $stories
    ]);
  }
}