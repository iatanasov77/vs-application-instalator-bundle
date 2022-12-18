<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;

use Vankosoft\ApplicationBundle\Command\ContainerAwareCommand;

final class ClearInstallCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'vankosoft:clear-install';
    
    protected function configure(): void
    {
        $this
            ->setDescription( 'Clear VankoSoft Application Installation.' )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command clear installation of VankoSoft Application.
EOT
            )
            ->addArgument( 'application', InputArgument::REQUIRED, 'The Application Name to be cleared.' )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $applicationName    = $input->getArgument( 'application' );
        $appSetup           = $this->get( 'vs_application.installer.setup_application' );
        
        $outputStyle        = new SymfonyStyle( $input, $output );
        
        // Clear Directories
        $outputStyle->writeln( '<info>Clear Directories Created from Installation of VankoSoft Application...</info>' );
        foreach ( $appSetup->getApplicationDirectories( $applicationName ) as $dir ) {
            exec( 'rm -rf ' . $dir );
        }
        $outputStyle->writeln( '<info>Application Directories successfully cleared.</info>' );
        
        // Drop Database
        $outputStyle->writeln( '<info>Drop Database Created from Installation of VankoSoft Application...</info>' );
        $command    = $this->getApplication()->find( 'doctrine:schema:drop' );
        $returnCode = $command->run( new ArrayInput( ['--force' => true] ), $output );
        $outputStyle->writeln( '<info>Database successfully dropped.</info>' );
        
        $outputStyle->newLine();
        
        return 0;
    }
}
