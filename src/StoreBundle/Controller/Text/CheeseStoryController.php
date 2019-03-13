<?php

namespace StoreBundle\Controller\Text;

use StoreBundle\DataAdapter\Text\CheeseStoryAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheeseStoryController extends Controller
{
  /**
   * @param Request $request
   * @return Response|JsonResponse
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
    
    return $this->render('@Store/CheeseStory/index.html.twig', [
      'stories' => $stories
    ]);
  }
  
  /**
   * @param $slug int|string
   * @return Response
   */
  public function showAction($slug) : Response
  {
    $story = $this->getDoctrine()->getRepository('StoreBundle:Text\CheeseStory')->findOneBy([
      'slug' => $slug
    ]);
    
    if(!$story || !$story->isPublished())
    {
      throw new NotFoundHttpException("Заметка с slug $slug не найдена!");
    }
    
    return $this->render('@Store/CheeseStory/show.html.twig', [
      'story' => $story
    ]);
  }
}