<?php namespace VS\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use VS\ApplicationBundle\Model\Interfaces\SettingsInterface;

use VS\ApplicationBundle\Model\Interfaces\SiteInterface;
use VS\CmsBundle\Model\PageInterface;

class GeneralSettingsExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $settingsFactory;
    
    /** @var FactoryInterface */
    private $siteFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    public function __construct( FactoryInterface $settingsFactory,  FactoryInterface $siteFactory )
    {
        $this->settingsFactory  = $settingsFactory;
        $this->siteFactory      = $siteFactory;
        $this->optionsResolver  = new OptionsResolver();
        
        $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): SettingsInterface
    {
        $options    = $this->optionsResolver->resolve( $options );
        
        $settingsEntity = $this->settingsFactory->createNew();
        
        // Set Site If Passed
        if ( isset( $options['siteTitle'] ) && $options['siteTitle'] ) {
            $site   = $this->siteFactory->createNew();
            $site->setTitle( $options['siteTitle'] );
            $settingsEntity->setSite( $site );
        } else {
            $settingsEntity->setSite( null );
        }
        
        $settingsEntity->setMaintenanceMode( $options['maintenanceMode'] );
        $settingsEntity->setMaintenancePage( $options['maintenancePage'] );
        $settingsEntity->setTheme( $options['theme'] );
        
        return $settingsEntity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'siteTitle', null )
            ->setAllowedTypes( 'siteTitle', ['null', 'string'] )
            
            ->setDefault( 'maintenanceMode', false )
            ->setAllowedTypes( 'maintenanceMode', 'bool' )
            
            ->setDefault( 'maintenancePage', null )
            ->setAllowedTypes( 'maintenancePage', ['null', PageInterface::class] )
            
            ->setDefault( 'theme', null )
            ->setAllowedTypes( 'theme', ['null', 'string'] )
        ;
    }
    
}
