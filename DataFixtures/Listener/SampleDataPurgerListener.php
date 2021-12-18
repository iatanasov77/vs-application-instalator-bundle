<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Listener;

use Sylius\Bundle\FixturesBundle\Listener\AbstractListener;
use Sylius\Bundle\FixturesBundle\Listener\BeforeSuiteListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * MANUAL FOR CUSTOM LISTENER: DirectoryPurgerListener
 * ========================================================
 * https://github.com/Sylius/SyliusFixturesBundle/blob/master/docs/custom_listener.md
 */
final class SampleDataPurgerListener extends AbstractListener implements BeforeSuiteListenerInterface
{
    /** @var ManagerRegistry */
    private $managerRegistry;
    
    /** @var array */
    private static $purgeModes = [
        'delete'    => ORMPurger::PURGE_MODE_DELETE,
        'truncate'  => ORMPurger::PURGE_MODE_TRUNCATE,
    ];
    
    public function __construct( ManagerRegistry $managerRegistry )
    {
        $this->managerRegistry  = $managerRegistry;
    }
    
    public function getName(): string
    {
        return 'sample_data_purger';
    }

    public function beforeSuite( SuiteEvent $suiteEvent, array $options ): void
    {
        foreach ( $options['managers'] as $managerName ) {
            /** @var EntityManagerInterface $manager */
            $manager    = $this->managerRegistry->getManager( $managerName );
            
            $purger     = new ORMPurger( $manager, $options['exclude'] );
            $purger->setPurgeMode( static::$purgeModes[$options['mode']] );
            $purger->purge();
        }
    }
    
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNodeBuilder = $optionsNode->children();
        
        $optionsNodeBuilder
            ->enumNode( 'mode' )
                ->values( ['delete', 'truncate'] )
                ->defaultValue( 'delete' )
        ;
        
        $optionsNodeBuilder
            ->arrayNode( 'managers' )
                ->defaultValue( [null] )
                ->scalarPrototype()
        ;
        
        $optionsNodeBuilder
            ->arrayNode( 'exclude' )
                ->defaultValue( [] )
                ->scalarPrototype()
        ;
    }
}