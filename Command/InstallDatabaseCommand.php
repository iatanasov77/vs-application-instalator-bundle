<?php namespace VS\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InstallDatabaseCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'vankosoft:install:database';

    protected function configure(): void
    {
        $this
            ->setDescription( 'Install VankoSoft Application database.' )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates VankoSoft Application database.
EOT
            )
            ->addOption( 'fixture-suite', 's', InputOption::VALUE_OPTIONAL, 'Load specified fixture suite during install', null )
            ->addOption( 'debug-commands', 'd', InputOption::VALUE_OPTIONAL, 'Debug Executed Commands', null )
        ;
    }

    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $suite = $input->getOption( 'fixture-suite' );
        $debug = $input->getOption( 'debug-commands' );
        
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( sprintf(
            'Creating VankoSoft Application database for environment <info>%s</info>.',
            $this->getEnvironment()
        ) );
        
        if ( null !== $debug ) {
            $outputStyle->newLine();
            $outputStyle->writeln( '<info>Displaying Debug Information ...</info>' );
            $this->commandExecutor->runCommand( 'doctrine:database:create', [], $output );
            $this->commandExecutor->runCommand( 'doctrine:migrations:list', [], $output );
            $this->commandExecutor->runCommand( 'doctrine:database:drop', ['--force' => true], $output );
            $outputStyle->newLine();
            
            throw new \RuntimeException( 'Debugging Only.' );
        } else {
            $commands = $this
                ->getContainer()
                ->get( 'vs_app.commands_provider.database_setup' )
                ->getCommands( $input, $output, $this->getHelper( 'question' ) )
            ;
            
            $this->runCommands( $commands, $output );
            $outputStyle->newLine();
        }
        
        return 0;
    }
}
