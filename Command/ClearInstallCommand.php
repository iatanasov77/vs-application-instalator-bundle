<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;

use Vankosoft\ApplicationBundle\Command\ContainerAwareCommand;

#[AsCommand(
    name: 'vankosoft:clear-install',
    description: 'Clear VankoSoft Application Installation.',
    hidden: false
)]
final class ClearInstallCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this
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
        
        return Command::SUCCESS;
    }
}
