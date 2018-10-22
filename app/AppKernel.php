<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            // These are the other bundles the SonataAdminBundle relies on
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),

            // And finally, the storage and SonataAdminBundle
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),

            new \Pix\SortableBehaviorBundle\PixSortableBehaviorBundle(),

            new Accurateweb\ShippingBundle\AccuratewebShippingBundle(),
//            new Accurateweb\CdekShippingBundle\AccuratewebCdekShippingBundle(),
            new Accurateweb\SphinxSearchBundle\AccuratewebSphinxSearchBundle(),
            new Accurateweb\PaymentBundle\AccuratewebPaymentBundle(),
            new Accurateweb\ImagingBundle\AccuratewebImagingBundle(),
            new Accurateweb\MediaBundle\AccuratewebMediaBundle(),
            new Accurateweb\EmailTemplateBundle\AccuratewebEmailTemplateBundle(),
            new Accurateweb\FilteringBundle\AccuratewebFilteringBundle(),
            new Accurateweb\ClientApplicationBundle\AccuratewebClientApplicationBundle(),
            new Accurateweb\SeoBundle\AccuratewebSeoBundle(),
            new Accurateweb\SlugifierBundle\AccuratewebSlugifierBundle(),
            new RedCode\TreeBundle\RedCodeTreeBundle(),
            new Accurateweb\SynchronizationBundle\SynchronizationBundle(),
            new Accurateweb\SettingBundle\AccuratewebSettingBundle(),
            new Accurateweb\TaxonomyBundle\TaxonomyBundle(),
            new Accurateweb\MetaBundle\AccuratewebMetaBundle(),
            new Accurateweb\LocationBundle\AccuratewebLocationBundle(),
            new Accurateweb\ContentHotspotBundle\ContentHotspotBundle(),
            new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),
            new Accurateweb\LogisticBundle\AccuratewebLogisticBundle(),

            new StoreBundle\StoreBundle(),
            new AppBundle\AppBundle(),
  
            new Accurateweb\MoyskladIntegrationBundle\AccuratewebMoyskladIntegrationBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
