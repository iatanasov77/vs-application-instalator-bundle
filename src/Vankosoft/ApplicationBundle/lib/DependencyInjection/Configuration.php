<?php namespace VS\ApplicationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Locale\Model\LocaleInterface;

use VS\ApplicationBundle\Model\Locale;

use VS\ApplicationBundle\Repository\ApplicationRepository;
use VS\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use VS\ApplicationBundle\Model\Application;
use VS\ApplicationBundle\Form\ApplicationForm;

use VS\ApplicationBundle\Repository\SettingsRepository;
use VS\ApplicationBundle\Model\Settings;
use VS\ApplicationBundle\Controller\SettingsController;
use VS\ApplicationBundle\Form\SettingsForm;

use VS\ApplicationBundle\Repository\TaxonomyRepository;
use VS\ApplicationBundle\Model\Taxonomy;
use VS\ApplicationBundle\Controller\TaxonomyController;
use VS\ApplicationBundle\Form\TaxonomyForm;

use VS\ApplicationBundle\Repository\TaxonRepository;
use VS\ApplicationBundle\Model\Taxon;
use VS\ApplicationBundle\Model\Interfaces\TaxonInterface;
use VS\ApplicationBundle\Form\TaxonForm;
//use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonTranslationType;
use VS\ApplicationBundle\Model\TaxonTranslation;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
//use Sylius\Component\Taxonomy\Factory\TaxonFactory;

use VS\ApplicationBundle\Model\Translation;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;

use VS\ApplicationBundle\Model\LogEntry;
use VS\ApplicationBundle\Repository\LogEntryRepository;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder    = new TreeBuilder( 'vs_application' );
        $rootNode       = $treeBuilder->getRootNode();
        
        // Main Config
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode( 'prepend_doctrine_migrations' )->defaultTrue()->end()
                ->scalarNode( 'orm_driver' )->defaultValue( SyliusResourceBundle::DRIVER_DOCTRINE_ORM )->cannotBeEmpty()->end()
                ->arrayNode( 'taxonomy' )
                    ->scalarPrototype()->end()
                ->end()
                ->scalarNode( 'locale' )->defaultValue( 'en_US' )->cannotBeEmpty()->end()
            ->end()
        ;
        
        // Menu Config
        $rootNode
            ->children()
                ->variableNode( 'menu' )->end()
            ->end()
        ;
        
        // Resources Config
        $this->addResourcesSection( $rootNode );
            
        return $treeBuilder;
    }

    private function addResourcesSection( ArrayNodeDefinition $node ): void
    {
        $node
            ->children()
                ->arrayNode( 'resources' )
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode( 'application' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Application::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( ApplicationInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( ApplicationRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( ApplicationForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode( 'settings' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Settings::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( ResourceInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( SettingsController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( SettingsRepository::class )->cannotBeEmpty()->end()
                                        //->scalarNode( 'repository' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( SettingsForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode( 'taxonomy' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Taxonomy::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( ResourceInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( TaxonomyController::class )->cannotBeEmpty()->end()
                                        //->scalarNode( 'repository' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( TaxonomyRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( TaxonomyForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode( 'taxon' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Taxon::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( TaxonInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->cannotBeEmpty()->end()
                                        //->scalarNode( 'repository' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( TaxonRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( TaxonForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                    
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue( TaxonTranslation::class )->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue( TaxonTranslationInterface::class )->cannotBeEmpty()->end()
                                                ->scalarNode('controller')->defaultValue( ResourceController::class )->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue( Factory::class )->end()
                                                //->scalarNode('form')->defaultValue( TaxonTranslationType::class )->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                
                            ->end()
                        ->end()
                        ->arrayNode( 'translation' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Translation::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( ResourceInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( TranslationRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode( 'logentry' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( LogEntry::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( ResourceInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( LogEntryRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode( 'locale' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Locale::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( LocaleInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( ResourceController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->end()
                                        //->scalarNode( 'form' )->defaultValue( LocaleType::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                    ->end()
                ->end()
            ->end()
        ;
    }
}
