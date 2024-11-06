<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 15.06.2017
 * Time: 18:45
 */

namespace AccurateCommerce\Search\History;


class SearchHistory
{
  static public function registerSearch($query, $nbResults)
  {
    return null;
//    $history = new SearchHistory();
//    $history->setResults($nbResults);
//    $history->setRequest($query);
//    $history->setClientIp($_SERVER['REMOTE_ADDR']);
//    $history->save();
//
//    return $history->getId();
  }

  /**
   * Регистрирует переход из поиска
   *
   * @param int $sid Идентификатор поиска
   * @param String $uri
   */
  static public function registerTransition($sid, $uri)
  {
    $destUri = null;
    if (is_string($uri))
    {
      $destUri = $uri;
    }
    else
    {
      if (null !== $uri)
      {
        sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
        $destUri = url_for($uri->getHref());
      }
    }

    $history = SearchHistoryQuery::create()->findPk($sid);
    if ($history)
    {
      $transitions = explode(';', $history->getTransitions());
      if (false === $transitions)
      {
        $transitions = array($destUri);
      }
      else
      {
        if (!in_array($destUri, $transitions))
        {
          $transitions[] = $destUri;
        }
      }
      $history->setTransitions(implode(';', $transitions));
      $history->save();
    }
  }

}