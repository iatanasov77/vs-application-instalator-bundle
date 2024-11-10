<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\SettingsInterface;

use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use Vankosoft\CmsBundle\Model\Interfaces\PageInterface;

class GeneralSettingsExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $settingsFactory;
    
    /** @var FactoryInterface */
    private $applicationsFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    public function __construct( FactoryInterface $settingsFactory,  FactoryInterface $applicationsFactory )
    {
        $this->settingsFactory      = $settingsFactory;
        $this->applicationsFactory  = $applicationsFactory;
        $this->optionsResolver      = new OptionsResolver();
        
        $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): SettingsInterface
    {
        $options    = $this->optionsResolver->resolve( $options );
        
        $settingsEntity = $this->settingsFactory->createNew();
        
        // Set Application If Passed
        if ( isset( $options['applicationTitle'] ) && $options['applicationTitle'] ) {
            $application    = $this->applicationsFactory->createNew();
            $application->setTitle( $options['applicationTitle'] );
            $settingsEntity->setApplication( $application );
        } else {
            $settingsEntity->setApplication( null );
        }
        
        $settingsEntity->setMaintenanceMode( $options['maintenanceMode'] );
        $settingsEntity->setMaintenancePage( $options['maintenancePage'] );
        $settingsEntity->setTheme( $options['theme'] );
        
        return $settingsEntity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'applicationTitle', null )
            ->setAllowedTypes( 'applicationTitle', ['null', 'string'] )
            
            ->setDefault( 'maintenanceMode', false )
            ->setAllowedTypes( 'maintenanceMode', 'bool' )
            
            ->setDefault( 'maintenancePage', null )
            ->setAllowedTypes( 'maintenancePage', ['null', PageInterface::class] )
            
            ->setDefault( 'theme', null )
            ->setAllowedTypes( 'theme', ['null', 'string'] )
        ;
    }
    
}
