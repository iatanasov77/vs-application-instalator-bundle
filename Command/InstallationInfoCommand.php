<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;
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
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( \sprintf( '<info>Current Library Version: %s</info>', $this->getVankosoftApplicationLibraryVersion() ) );
        return Command::SUCCESS;
        
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
        $outputStyle    = new SymfonyStyle( $input, $output );
        
        $currentVersion = $this->getCurrentVersion();
        if ( $currentVersion === InstalationInfoInterface::VERSION_UNDEFINED ) {
            $outputStyle->writeln( '<error>Missing VERSION file.</error>' );
            
            return Command::FAILURE;
        }
        
        $versionInfo    = $this->getVersionInfo( $currentVersion );
        
        if ( ! $versionInfo->getId() ) {
            $outputStyle->writeln( \sprintf( '<error>Missing Version Info for Version: %s.</error>', $currentVersion ) );
            
            return Command::FAILURE;
        }
        
        $versionData    = $versionInfo->getData();
        $outputStyle->writeln( \sprintf( '<info>Current Version: %s</info>', $versionData[InstalationInfoInterface::VERSION_DATA_PROJECT_VERSION] ) );
        $outputStyle->writeln( \sprintf( '<info>Current Migration: %s</info>', $versionData[InstalationInfoInterface::VERSION_DATA_DOCTRINE_MIGRATION] ) );
        $outputStyle->writeln( \sprintf( '<info>Current Library Version: %s</info>', $versionData[InstalationInfoInterface::VERSION_DATA_VANKOSOFT_APPLICATION_VERSION] ) );
        
        return Command::SUCCESS;
    }
    
    private function updateProjectInstallationInfo( InputInterface $input, OutputInterface $output ): int
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        
        $currentVersion = $this->getCurrentVersion();
        if ( $currentVersion === InstalationInfoInterface::VERSION_UNDEFINED ) {
            $outputStyle->writeln( '<error>Missing VERSION file.</error>' );
            
            return Command::FAILURE;
        }
        
        $versionInfo    = $this->getVersionInfo( $currentVersion );
        $versionData        = [
            InstalationInfoInterface::VERSION_DATA_PROJECT_VERSION                  => $currentVersion,
            InstalationInfoInterface::VERSION_DATA_DOCTRINE_MIGRATION               => $this->getCurrentDoctrineMigration(),
            InstalationInfoInterface::VERSION_DATA_VANKOSOFT_APPLICATION_VERSION    => $this->getVankosoftApplicationLibraryVersion(),
        ];
        $versionInfo->setData( $versionData );
        
        $entityManager      = $this->get( 'doctrine' )->getManager();
        $entityManager->persist( $versionInfo );
        $entityManager->flush();
        
        return Command::SUCCESS;
    }
    
    private function getCurrentVersion(): string
    {
        $filesystem     = new Filesystem();
        $versionFile    = $this->getParameter( 'kernel.project_dir' ) . '/VERSION';
        $currentVersion = $filesystem->exists( $versionFile ) ?
                            \file_get_contents( $versionFile ) :
                            InstalationInfoInterface::VERSION_UNDEFINED;
        
        return \trim( $currentVersion );
    }
    
    private function getVersionInfo( string $currentVersion ): InstalationInfoInterface
    {
        $repo           = $this->get( 'vs_application_instalator.repository.instalation_info' );
        $versionInfo    = $repo->findOneBy( ['version' => $currentVersion] );
        if ( ! $versionInfo ) {
            $factory        = $this->get( 'vs_application_instalator.factory.instalation_info' );
            
            $versionInfo    = $factory->createNew();
            $versionInfo->setVersion( $currentVersion );
        }
        
        return $versionInfo;
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
    
    private function getVankosoftApplicationLibraryVersion(): string
    {
        $composerInfo   = $this->get( 'vs_application.composer_info' )->getInstalledPackagesInfo();
        var_dump( \array_keys( $composerInfo ) ); exit;
        
        return $composerInfo['vankosoft/application']->getRawVersion();
    }
    
    private function getVankosoftApplicationExtensionCatalogVersion(): string
    {
        return '';
    }
}
