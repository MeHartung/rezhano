<?php


namespace StoreBundle\Command\Product;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixSlugCommand extends ContainerAwareCommand
{
  public function configure()
  {
    $this->setName('product:slug:fix');
  }
  
  public function execute(InputInterface $input, OutputInterface $output)
  {
    $em = $this->getContainer()->get('doctrine')->getManager();
    $repo = $em->getRepository('StoreBundle:Store\Catalog\Product\Product');
    
    $slugs = [];
    
    foreach ($repo->findAll() as $product)
    {
      $slugs[$product->getSlug()][] = $product->getId();
    }
    
    $duplicatedSlugs = [];
    array_filter($slugs, function ($slug, $key) use (&$duplicatedSlugs)
    {
      if (count($slug) >= 2) $duplicatedSlugs[$key] = $slug;
    }, ARRAY_FILTER_USE_BOTH);
    
    if (count($duplicatedSlugs) === 0)
    {
      $output->writeln('Duplicates not found!');
      return;
    }
    
    foreach ($duplicatedSlugs as $duplicatedSlug => $productIdx)
    {
      # товар, к слагу которого не прицепляется порядковый номер
      $mainProduct = null;
      $publishedProducts = $repo->findBy([
        'slug' => $duplicatedSlug,
        'published' => 1
      ]);
      
      $nbPublished = count($publishedProducts);
      
      if ($nbPublished > 1)
      {
        $output->writeln(sprintf('Find %s published products with slug %s: %s. Please, resolve this manual.',
          $nbPublished, $duplicatedSlug, explode(', ', $productIdx)));
        continue;
      }
      
      if ($nbPublished === 1)
      {
        $mainProduct = $publishedProducts[0];
        $output->writeln('Product with id ' . $mainProduct . ' is published.');
      }
      
      $notChangedId = $mainProduct === null ? $productIdx[0] : $mainProduct->getId();
      $output->writeln('For product with id ' . $notChangedId . ' slug not change.');
      
      unset($productIdx[array_search($notChangedId, $productIdx, true)]);
      
      $i = 1;
      foreach ($productIdx as $id)
      {
        $changedProduct = $repo->find($id);
        $newSlug = sprintf('%s-%s', $changedProduct->getSlug(), $i);
        $changedProduct->setSlug($newSlug);
        $em->persist($changedProduct);
        
        $output->writeln(sprintf('For product %s slug change to %s', $changedProduct->getId(),
          $changedProduct->getSlug()));
      }
      
      $em->flush();
    }
  }
}