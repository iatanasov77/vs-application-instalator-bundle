<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Vankosoft\ApplicationInstalatorBundle\Model\InstalationInfoInterface;

/**
 * When Create New Release Run this Command after 'bumpversion' Command
 * to see the Actual Project Version in Web Interface
 */
#[AsCommand(
    name: 'vankosoft:install:info',
    description: 'Checks if all VankoSoft Application requirements are satisfied.',
    hidden: false
)]
final class InstallationInfoCommand extends AbstractInstallCommand
{
    protected function configure(): void
    {
        $this
            ->setHelp(<<<EOT
The <info>%command.name%</info> command checks system requirements.
EOT
            )
            ->addArgument( 'action', InputArgument::OPTIONAL, 'The Installation Info Action to be Handled.' )
        ;
    }

    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $action = $input->getArgument( 'action' );
        
        switch ( $action ) {
            case 'update':
                return $this->updateProjectInstallationInfo( $input, $output );
                break;
            default:
                return $this->showProjectInstallationInfo( $input, $output );
        }
    }
    
    private function showProjectInstallationInfo( InputInterface $input, OutputInterface $output ): int
    {
        $outputStyle        = new SymfonyStyle( $input, $output );
        
        $currentMigration   = $this->getCurrentDoctrineMigration();
        $outputStyle->writeln( \sprintf( '<info>Current Migration: %s</info>', $currentMigration ) );
        
        $outputStyle->newLine();
        
        return Command::SUCCESS;
    }
    
    private function updateProjectInstallationInfo( InputInterface $input, OutputInterface $output ): int
    {
        $filesystem     = new Filesystem();
        $versionFile    = $this->getParameter( 'kernel.project_dir' ) . '/VERSION';
        $currentVersion = $filesystem->exists( $versionFile ) ?
                            \file_get_contents( $versionFile ) :
                            InstalationInfoInterface::VERSION_UNDEFINED;
        
        if ( $currentVersion === InstalationInfoInterface::VERSION_UNDEFINED ) {
            
            
            return Command::FAILURE;
        }
        
        $repo           = $this->get( 'vs_application_instalator.repository.instalation_info' );
        $versionInfo    = $repo->findOneBy( ['version' => $currentVersion] );
        if ( ! $versionInfo ) {
            $factory        = $this->get( 'vs_application_instalator.factory.instalation_info' );
            $versionInfo    = $factory->createNew();
        }
        
        $versionData        = [
            InstalationInfoInterface::VERSION_DATA_PROJECT_VERSION      => $currentVersion,
            InstalationInfoInterface::VERSION_DATA_DOCTRINE_MIGRATION   => $this->getCurrentDoctrineMigration(),
        ];
        $versionInfo->setData( $versionData );
        
        $entityManager      = $this->get( 'doctrine' )->getManager();
        $entityManager->persist( $versionInfo );
        $entityManager->flush();
        
        return Command::SUCCESS;
    }
    
    /**
     * Example Commands
     * ================
     * bin/console doctrine:migrations:current
     * bin/console doctrine:migrations:latest
     * 
     * @return string
     */
    private function getCurrentDoctrineMigration(): string
    {
        $output = new BufferedOutput();
        $this->commandExecutor->runCommand( 'doctrine:migrations:current', [], $output );
        $currentMigration   = $output->fetch();
        
        return $currentMigration;
    }
}
