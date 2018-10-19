<?php

namespace StoreBundle\Jstree;

  /*
   * To change this license header, choose License Headers in Project Properties.
   * To change this template file, choose Tools | Templates
   * and open the template in the editor.
   */
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;

/**
   * Description of CatalogSectionAdminClientModel
   *
   * @author user
   */
  class CatalogSectionAdminClientModel {

      protected static
              $advantagesList = null;
      protected
              /** @var Taxon */
              $section = null;

      public function __construct(Taxon $section)
      {
          $this->section = $section;
      }

      public function getClientModelId()
      {
          return $this->section->getClientModelId();
      }

      public function getClientModelName()
      {
          return $this->section->getClientModelName();
      }

      public function getClientModelValues($context = null)
      {
          //sfContext::getInstance()->getLogger()->debug('CatalogSection::admin-client');
          
          $fontIcon = $this->section->getIcon();
          $fontIconClientModel =  $fontIcon ? new FontIconClientModel($fontIcon) : null;
          
          $values = array(              
              'id'                                => $this->section->getId(),
              'name'                              => $this->section->getName(),             
              'catalog_id'                        => $this->section->getCatalogId(),
              'children'                          => $this->getChildrens(),
              'icon'                              => $fontIconClientModel ? json_encode($fontIconClientModel->getClientModelValues()) : null
          );
          
          if ('rest_api' == $context)
          {
            $image = $this->section->getImage();
            $advantagesIds = array();

            $advantagesList = CatalogSectionAdminClientModel::getAdvantagesList();

            foreach ($this->section->getAdvantages() as $advantage)
            {
                $advantagesIds[] = $advantage->getId();
            }

            $presentationId = $this->section->getPresentationId();
            if (null === $presentationId)
            {
                $presentationId = $this->section->getPresentationProvider()
                                                  ->getDefaultPresentation($this->section)
                                                  ->getId();
            }

            $presentationConfigurationManager = CatalogSectionPresentationConfigurationManager::getInstance();
            $presentationsArr = array();

            $presentationMap = $this->section->getPresentationProvider()->getPresentationMap();
            $applicablePresentations = $presentationMap->getNames();
            foreach ($applicablePresentations as $id => $name)
            {
              $applicablePresentation = $presentationMap->create($id, $this->section);
              $presentationConfigurationSchema = CatalogSectionPresentationConfigurationSchemaFactory::create($applicablePresentation);
              $presentationsArr[$id] = array(
                  'id' => $id,
                  'name' => $name,
                  'options' => array(
                    'schema' => $presentationConfigurationSchema->getClientFormSchema(),
                    'values' => $presentationConfigurationManager->getCatalogSectionPresentationOptions($applicablePresentation)  
                  )
              );
            }
          
            $values = array_merge($values, array(
              
              'alias'                             => $this->section->getAlias(),
              'image'                             => $image->file()->exists() ? $image->web()->url() : '',
              'image_x1'                          => $this->section->getImageX1(),
              'image_x2'                          => $this->section->getImageX2(),
              'image_y1'                          => $this->section->getImageY1(),
              'image_y2'                          => $this->section->getImageY2(),
              'catalog_section_to_advantage_list' => $advantagesIds,
              'catalog_section_to_advantage'      => $advantagesList,
              'on_homepage'                       => $this->section->getOnHomepage(),
              'menu_name'                         => $this->section->getMenuName(),
              'name_genitive_plural'              => $this->section->getNameGenitivePlural(),
              'presentations'                     => $presentationsArr,
              'presentation_id'                   => $presentationId,
              'url'                               => sfContext::getInstance()->getController()->genUrl($this->section->getRoute()),
              'saved_filters'                     => $this->getRelatedFilters(),
              'icon_chars'                        => ClientApplicationModelCollection::createFromClientModelArray(sprCatalogSectionIconManager::load()->getIconClientModels())->toArray(),
              'knowledge'                         => $this->getRelatedKnowledge()
            ));
          }

          return $values;
      }
      
      
      public function getRelatedFilters()
      { 
         $relationsArr = array();
         $relations = $this->section->getCatalogSectionToSavedFiltersJoinSavedFilter(CatalogSectionToSavedFilterQuery::create()->orderByRank()); 
         
         foreach($relations as $relation)
         {
             $relationsArr[] = $relation->getClientModelValues();
         }
         
         return $relationsArr;
      }

      public function getRelatedKnowledge()
      {
        $relationsArr = array();
        $relations = $this->section->getKnowledgeBaseArticleToCatalogSections(KnowledgeBaseArticleToCatalogSectionQuery::create()->orderByRank());

        foreach($relations as $relation)
        {
          $relationsArr[] = $relation->getClientModelValues();
        }

        return $relationsArr;
      }
      
      public function getChildrens()
      {
          $children = array();

          $childSections = $this->section->getChildren();
          
          
          foreach ($childSections as $section)
          {
              $clientModel = new CatalogSectionAdminClientModel($section);
              $children[] = $clientModel->getClientModelValues();
          }
          
          return $children;
      }

      protected static function getAdvantagesList()
      {
          if (self::$advantagesList === null)
          {
              self::$advantagesList = array();

              $advantages = AdvantageQuery::create()->find();

              foreach ($advantages as $advantage)
              {
                  self::$advantagesList[$advantage->getId()] = $advantage->getName();
              }
          }

          return self::$advantagesList;
      }

  }
  