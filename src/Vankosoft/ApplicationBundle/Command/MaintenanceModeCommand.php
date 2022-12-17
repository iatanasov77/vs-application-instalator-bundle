<?php namespace Vankosoft\ApplicationBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

class MaintenanceModeCommand extends ContainerAwareCommand
{    
    protected static $defaultName = 'vankosoft:maintenance';
    
    protected function configure()
    {
        $this
            ->setDescription( 'Manage Maintenance Mode with CLI. Use it in deployment scrpipts for example.' )
            ->setHelp( 'Manage Maintenance Mode with CLI.' )
            
            ->addOption( 'set-maintenance', null, InputOption::VALUE_NONE, 'Set In Maintennce Mode.')
            ->addOption( 'unset-maintenance', null, InputOption::VALUE_NONE, 'Unset Maintennce Mode.')
            ->addOption( 'dump-settings', null, InputOption::VALUE_NONE, 'Dump Settings Array (Using for debug).')
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $io         = new SymfonyStyle( $input, $output );
        $io->newLine();
        
        if ( $input->getOption( 'set-maintenance' ) ) {
            $this->get( 'vs_app.settings_manager' )->forceMaintenanceMode( true );
        }
        
        if ( $input->getOption( 'unset-maintenance' ) ) {
            $this->get( 'vs_app.settings_manager' )->forceMaintenanceMode( false );
        }
        
        if ( $input->getOption( 'dump-settings' ) ) {
            $allSettings    = $this->get( 'vs_app.settings_manager' )->getAllSettings();
            print_r( $allSettings );
        }
        
        return 0;
    }
}
