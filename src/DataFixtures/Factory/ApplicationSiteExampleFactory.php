<?php namespace VS\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use VS\ApplicationBundle\Model\Interfaces\SettingsInterface;

use VS\ApplicationBundle\Model\Interfaces\SiteInterface;
use VS\CmsBundle\Model\PageInterface;

class ApplicationSiteExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $sitesFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    public function __construct( FactoryInterface $sitesFactory )
    {
        $this->sitesFactory     = $sitesFactory;
        $this->optionsResolver  = new OptionsResolver();
        
        $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): SiteInterface
    {
        $options    = $this->optionsResolver->resolve( $options );
        $siteEntity = $this->sitesFactory->createNew();
        
        $siteEntity->setTitle( $options['title'] );
        
        return $siteEntity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'title', null )
            ->setAllowedTypes( 'title', ['string'] )
        ;
    }
    
}
