<?php namespace Vankosoft\ApplicationBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
//use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Container\ContainerInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @link https://github.com/symfony/symfony/blob/v4.4.18/src/Symfony/Bundle/FrameworkBundle/Command/ContainerAwareCommand.php
 */
abstract class ContainerAwareCommand extends Command implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;
    
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var ValidatorInterface */
    private $validator;
    
    public function __construct(
        ContainerInterface $container,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator
    ) {
        parent::__construct();
        
        $this->container    = $container;
        
        $this->doctrine     = $doctrine;
        $this->validator    = $validator;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setContainer( ContainerInterface $container = null )
    {
        $this->container    = $container;
    }
    
    /**
     * @return object
     */
    protected function get( string $id )
    {
        switch ( $id ) {
            case 'doctrine':
                return $this->doctrine;
                break;
            case 'validator':
                return $this->validator;
                break;
            default:
                return $this->getContainer()->get( $id );
                
        }
    }
    
    protected function getParameter( string $id )
    {
        return $this->getContainer()->getParameter( $id );
    }
    
    /**
     * @return ContainerInterface
     *
     * @throws \LogicException
     */
    private function getContainer()
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
}
    
