<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Sylius\Component\Locale\Model\LocaleInterface;

class LocalesExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $localesFactory;
    
    /** @var RepositoryInterface */
    private $localesRepository;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    public function __construct(
        FactoryInterface $localesFactory,
        RepositoryInterface $localesRepository
    ) {
            $this->localesFactory       = $localesFactory;
            $this->localesRepository    = $localesRepository;
            
            $this->optionsResolver  = new OptionsResolver();
            $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): LocaleInterface
    {
        $options    = $this->optionsResolver->resolve( $options );
        
        $localeEntity   = $this->localesRepository->findOneBy( ['code' => $options['code']]);
        if ( ! $localeEntity ) {
            $localeEntity = $this->localesFactory->createNew();
            
            $localeEntity->setCode( $options['code'] );
        }
        
        return $localeEntity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver            
            ->setDefault( 'code', null )
            ->setAllowedTypes( 'code', ['string'] )
        ;
    }
}
