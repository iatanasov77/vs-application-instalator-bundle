<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

use Vankosoft\ApplicationBundle\Model\Interfaces\TagsWhitelistTagInterface;

class TagsWhitelistTagsExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var RepositoryInterface */
    private $tagsWhitelistContextsRepository;
    
    /** @var FactoryInterface */
    private $tagsWhitelistTagsFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    public function __construct(
        RepositoryInterface $tagsWhitelistContextsRepository,
        FactoryInterface $tagsWhitelistTagsFactory
    ) {
        $this->tagsWhitelistContextsRepository  = $tagsWhitelistContextsRepository;
        $this->tagsWhitelistTagsFactory         = $tagsWhitelistTagsFactory;
        
        $this->optionsResolver                  = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): TagsWhitelistTagInterface
    {
        $options    = $this->optionsResolver->resolve( $options );
        
        $tagEntity  = $this->tagsWhitelistTagsFactory->createNew();
        $tagEntity->setTag( $options['tag'] );
        
        $context    = $this->tagsWhitelistContextsRepository->findByTaxonCode( $options['context_code'] );
        $tagEntity->setContext( $context );
        
        return $tagEntity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'context_code', null )
            ->setAllowedTypes( 'context_code', ['string'] )
        
            ->setDefault( 'tag', null )
            ->setAllowedTypes( 'tag', ['string'] )
        ;
    }
}