<?php namespace Vankosoft\ApplicationBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
//use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Container\ContainerInterface;

/**
 * @link https://github.com/symfony/symfony/blob/v4.4.18/src/Symfony/Bundle/FrameworkBundle/Command/ContainerAwareCommand.php
 */
abstract class ContainerAwareCommand extends Command implements ContainerAwareInterface
{
    private $container;
    
    public function __construct( ContainerInterface $container )
    {
        parent::__construct();
        $this->container    = $container;
    }
    
    /**
     * @return ContainerInterface
     *
     * @throws \LogicException
     */
    protected function getContainer()
    {
        if ( null === $this->container ) {
            $application    = $this->getApplication();
            if ( null === $application ) {
                throw new \LogicException( 'The container cannot be retrieved as the application instance is not yet set.' );
            }
            
            $this->container    = $application->getKernel()->getContainer();
        }
        
        return $this->container;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setContainer( ContainerInterface $container = null )
    {
        $this->container    = $container;
    }
}
    
