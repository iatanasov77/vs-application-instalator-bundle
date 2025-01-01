<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\SettingsInterface;

use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;

class ApplicationsExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $applicationsFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    public function __construct( FactoryInterface $applicationsFactory )
    {
        $this->applicationsFactory  = $applicationsFactory;
        $this->optionsResolver      = new OptionsResolver();
        
        $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): ApplicationInterface
    {
        $options            = $this->optionsResolver->resolve( $options );
        $applicationEntity  = $this->applicationsFactory->createNew();
        
        $applicationEntity->setTitle( $options['title'] );
        
        return $applicationEntity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'title', null )
            ->setAllowedTypes( 'title', ['string'] )
        ;
    }
    
}
