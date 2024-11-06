<?php

namespace Accurateweb\SphinxSearchBundle\Twig;

use AccurateCommerce\Search\CatalogSearch;

class SphinxSearchExtension extends \Twig_Extension
{
  public function getFunctions()
  {
    return [
      new \Twig_SimpleFunction('highlight_search_results', [$this, 'highlightSearchResults'])
    ];
  }

  public function highlightSearchResults(CatalogSearch $search, $string, $tag, $attributes)
  {
    $docs = [$string];

    $before_match = sprintf('<%1$s%2$s>', $tag,
      empty($attributes) ? '' : ' '.implode(' ', array_map(function($key) use ($attributes){
          return $key . '="'.$attributes[$key].'"';
        }, array_keys($attributes))));  // highlight
    $after_match = sprintf('</%s>', $tag);

    $excerpts = $search->buildExcerpts($docs, [
      'before_match' => $before_match,
      'after_match' => $after_match
    ]);

    if (false === strpos($excerpts[0], '<span class="highlight'))
    {
      return $this->hightlightSearchResultsManually($string, $search->getSphinxQuery(), $tag, $attributes);
    }

    return $excerpts[0];
  }

  public function hightlightSearchResultsManually($haystack, $needle, $tag = 'b', $attributes = array())
  {
    $stopwords = array(); // TODO

    if (strlen($haystack) == 0)
    {
      return false;
    }

    $words = preg_split('/\s+/u', $needle);

    if (empty($words))
    {
      return $haystack;
    }
    $hl = sprintf('<%1$s%2$s>\1</%1$s>', $tag,
      empty($attributes) ? '' : ' '.implode(' ', array_map(function($key) use ($attributes){
          return $key . '="'.$attributes[$key].'"';
        }, array_keys($attributes))));  // highlight
    $pattern = '/(%s)/iu';
    foreach ($words as $v)
    {
      $v = mb_convert_case($v, MB_CASE_LOWER, 'UTF-8');
      // limit (3) should be equal to mysql variable 'ft_min_word_len'
      if (strlen(trim($v)) == 0 || in_array($v, $stopwords) || strlen($v) < 3){
        continue; //  no empty words or stopwords
      }
      $qv = preg_quote($v, '/'); // regex quote
      //$qv1 = preg_quote(htmlentities($v));  // regex quote
      $regex = sprintf($pattern, $qv);
      $haystack = preg_replace($regex, $hl, $haystack);
//    if ($qv != $qv1)
//    {
//      $regex1 = sprintf($pattern, $qv1);
//      $haystack = preg_replace($regex1, $hl, $haystack);
//    }
    }
    return $haystack;
  }
}