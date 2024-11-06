<?php

namespace StoreBundle\Service\Uploader;

use StoreBundle\Entity\Document\Document;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\RouterInterface;

class DocumentStorage
{
  private $webDir;
  private $path;
  private $router;

  /**
   * DocumentStorage constructor.
   * @param string $webDir
   * @param string $path
   * @param RouterInterface $router
   */
  public function __construct ($webDir, $path, RouterInterface $router)
  {
    $this->webDir = $webDir;
    $this->path = $path;
    $this->router = $router;
  }

  /**
   * @param Document $document
   * @return boolean
   */
  public function isExists(Document $document)
  {
    $file = $this->getPath($document);
    return file_exists($file);
  }

  public function getExtension(Document $document)
  {
    return $this->getFile($document)->getExtension();
  }

  /**
   * absolute url
   * @param Document $document
   * @return string
   */
  public function getAbsoluteUrl(Document $document)
  {
    $url = $this->getUrl($document);
    return sprintf('%s://%s%s', $this->router->getContext()->getScheme(), $this->router->getContext()->getHost(), $url);
  }

  /**
   * relative url
   * @param Document $document
   * @return string
   */
  public function getUrl(Document $document)
  {
    return sprintf('/%s/%s', $this->path, $document->getFile());
  }

  /**
   * absolute path in file system
   * @param Document $document
   * @return string
   */
  public function getPath(Document $document=null)
  {
    $fileName = '';

    if ($document)
    {
      $fileName = $document->getFile();
    }

    return sprintf('%s/%s/%s', $this->webDir, $this->path, $fileName);
  }

  /**
   * @param Document $document
   * @return File
   * @throws FileNotFoundException
   */
  public function getFile(Document $document)
  {
    return new File($this->getPath($document));
  }
}