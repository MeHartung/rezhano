<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;

interface TaxonPresentationInterface
{
  const TAXON_PRESENTATION_PRODUCTS = 1;

  const TAXON_PRESENTATION_CHILD_SECTIONS = 2;
  /**
   * @return TaxonInterface
   */
  public function getTaxon();

  /**
   * @return string
   */
  public function getTemplateName();

  /**
   * @return array
   */
  public function getParameters();

  /**
   * @param $parameters array
   * @return TaxonPresentationInterface
   */
  public function setParameters($parameters);

  /**
   * @param $param string
   * @param $value
   * @return TaxonPresentationInterface
   */
  public function addParameter($param, $value);

  /**
   * @param $param string
   * @param $value
   * @return boolean
   */
  public function hasParameter($param, $value);

  public function prepare();

  /**
   * @return array
   */
  public function getProducts();
}