<?php
/**
 * Copyright (c) 2017. Denis N. Ragozin <dragozin@accurateweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace StoreBundle\Twig;

use AccurateCommerce\Build\AssetMap;


/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
class FrontendInteropTwigExtension extends \Twig_Extension
{
  private $rootDir,
          $assetMap;

  public function __construct($rootDir)
  {
    $this->rootDir = $rootDir;
    $this->assetMap = new AssetMap($this->rootDir);
  }

  public function getFunctions()
  {
    return array(
      new \Twig_SimpleFunction('get_versioned_javascripts', array($this, 'getVersionedJavascripts')),
      new \Twig_SimpleFunction('get_versioned_stylesheets', array($this, 'getVersionedStylesheets'))
    );
  }

  public function getVersionedJavascripts()
  {
    $js = array(
      sprintf('<script type="text/javascript">window.require = { waitSeconds: 600 };</script>'),
      sprintf('<script type="text/javascript" src="/js/vendor/requirejs/require.js" data-main="%s"></script>', $this->assetMap['staticAssets']['app']['js'][0])
    );

    return implode(PHP_EOL, $js);
  }

  public function getVersionedStylesheets()
  {
    $css = array(
      sprintf('<link rel="stylesheet" href="%s">', $this->assetMap['staticAssets']['app']['css'][0])
    );

    return implode(PHP_EOL, $css);
  }

  /**
   * Returns the name of the extension.
   *
   * @return string The extension name
   */
  public function getName()
  {
    return "FrontendInterop";
  }
}