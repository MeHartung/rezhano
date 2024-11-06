<?php

/**
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @package Artsofte\Plugins\asSynchronizationServicesPlugin\parser
 * @version SVN: $Id: SimpleXMLParser.class.php 137921 2014-06-30 07:57:01Z selezenev $
 * @revision SVN: $Revision: 137921 $
 */

namespace StoreBundle\Synchronization\Parser;

use Accurateweb\SynchronizationBundle\Model\Entity\Base\BaseEntity;
use Accurateweb\SynchronizationBundle\Model\Parser\BaseParser;

/**
 * Описание класса SimpleXMLParser
 *
 * @author Dancy
 */
class SimpleXMLParser extends BaseParser
{
  private $childParsers;
  
  public function __construct($configuration, $subject, $entityFactory, $schema, $options = array())
  {
    parent::__construct($configuration, $subject, $entityFactory, $schema, $options);
    
    $this->childParsers = array();
    
    if (isset($options["children"]))
    {
      foreach ($options["children"] as $collectionTag => $options)
      {
        if (!is_array($options))
        {
          $options = array('subject' => $options);
        }
        
        $childSubject = $options['subject'];
        unset($options['subject']);
        
        $childParser = $this->getServiceConfiguration()->getParser($childSubject);
        if ($childParser instanceof SimpleXMLParser)
        {
            $this->addChildParser($childSubject, $childParser, $collectionTag, $options);
        }
      }
    }
  }
  
  public function addChildParser($subject, $parser, $collectionTag, $options=array())
  {
    $this->childParsers[$subject] = array("tag" => $collectionTag, "parser" => $parser, "options" => $options);
  }
  
  /**
   * Проблема этой функции в том, что она не поддерживает вложенные структуры однотипных объектов
   * @return type 
   */
  public function getChildParsers()
  {
    $child_parsers_children = array();
    foreach ($this->childParsers as $subject => $childParser)
    {
      $child_parsers_children[$subject] = $childParser["parser"]->getChildParsers();
    }
    
    return array_merge($this->childParsers, $child_parsers_children);
  }
  
  protected function loadFile($filename)
  {
    return simplexml_load_file($filename);
  }
  
  protected function getChildNotFoundMessage($collectionTagName, $collectionName = null, $parent = null)
  {
    return sprintf("<%s> child node not found. %s skipping... ", $collectionTagName, $collectionName);
  }


  /**
   * Выполняет разбор одной ветви XML
   * 
   * @param object $node   Текущий элемент
   * @param object $parent Родительский элемент
   */
  protected function parseNode($node, $parent=null)
  {
    /** @var  $entity BaseEntity */
    $entity = $this->createEntity();
    if (false !== $entity->parse($node, $parent))
    {    
      $this->entities->add($entity);
      foreach ($this->childParsers as $parser)
      {
        $collectionTagName = $parser["tag"];
        $tagName = $parser["parser"]->getOption("tagName");

        $type = isset($parser['options']['type']) ? $parser['options']['type'] : 'collection';
        if (!in_array($type, array('object', 'collection')))
        {
          throw new \InvalidArgumentException(sprintf('Child subject type must be one of: "object", "collection", "%s" given', $type));
        }

        $collectionName = $parser["parser"]->getOption("name");

        $childNodes = $node->$collectionTagName;

        if (!empty($childNodes))
        {        
          if ($type == 'object')
          {
            $parser["parser"]->parseNode($node->$collectionTagName, $node);
          }
          else
          {
            foreach ($node->$collectionTagName->$tagName as $childNode)
            {
              $parser["parser"]->parseNode($childNode, $node);
            }
          }
        }
        else
        {
          if (!isset($parser['options']['required']) || $parser['options']['required'])
          {
            //asSynchronizationLogger::getInstance ()->warn($this->getChildNotFoundMessage($collectionTagName, $collectionName, $node));
          }
        }
      }
    }
  }  
  
  /**
   * Выполняет разбор XML в коллекцию сущностей
   * 
   * @param type $source
   * @return SimpleXMLParser 
   */
  public function parse($source)
  {
    foreach ($source as $node) 
      $this->parseNode($node);
    
    return $this;
  }
  
  public function getEntities()
  {
    $childEntities = array($this->getSubject() => $this->entities);
    
    foreach ($this->childParsers as $subject => $childParser)
    {
      if ($childParser !== $this)
      {
        $e = $childParser["parser"]->getEntities();
        if (is_array($e))
          $childEntities = array_merge($childEntities, $e);
        else
          $childEntities[$subject] = $e;
      } 
      else
       $this->entities->add($childParser["parser"]->getEntities()); 
    }
    
    return $childEntities;
  }
  
  /**
   * Выполняет сериализацию набора сущностей в XML
   * 
   * @param type $objects
   * @return type 
   */
  public function serialize($objects)
  {
    $rootName = $this->getOption("rootName");
    $tagName = $this->getOption("tagName");
    
    $serialized = "";
    
    foreach ($objects as $object)
    {
      $serialized .= sprintf("  <%s>\n%s  </%s>", $tagName, $object->toSyncXml(), $tagName).PHP_EOL;
    }
    
    return sprintf(<<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<%s>
%s
</%s>
EOF
   , $rootName, $serialized, $rootName);
  }
}
