<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Command;

use Accurateweb\ImagingBundle\Adapter\GdImageAdapter;
use Accurateweb\ImagingBundle\Filter\GdFilterFactory;
use Accurateweb\MediaBundle\Generator\ImageThumbnailGenerator;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use StoreBundle\Entity\Store\Catalog\Product\ProductImage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class GenerateThumbnailsCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      ->setName('media:thumbnails:generate')
      ->setDescription('Generate all registered thumbnails for images')
      ->setHelp('Generates all registered thumbnails for images')
      ->addArgument('entity', null, 'Entity');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $iterator = $this
      ->getContainer()
      ->get('doctrine.orm.entity_manager')
      ->getRepository($input->getArgument('entity'))
      ->createQueryBuilder('m')
      ->getQuery()
      ->iterate()
    ;

    $mediaStorageProvider = $this->getContainer()->get('aw.media.storage.provider');

    $generator = new ImageThumbnailGenerator($mediaStorageProvider->getMediaStorage(null), new GdImageAdapter(), new GdFilterFactory());

    foreach ($iterator as $media)
    {
      if (!$media[0] instanceof ImageInterface)
      {
        continue;
      }

      try
      {
        $generator->generate($media[0]);
      }
      catch (FileNotFoundException $e)
      {
        $output->writeln($e->getMessage());
      }

    }
  }
}