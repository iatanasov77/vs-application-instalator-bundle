<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'vankosoft:install:database',
    description: 'Install VankoSoft Application database.',
    hidden: false
)]
final class InstallDatabaseCommand extends AbstractInstallCommand
{
    protected function configure(): void
    {
        $this
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates VankoSoft Application database.
EOT
            )
            ->addOption( 'debug-commands', 'd', InputOption::VALUE_OPTIONAL, 'Debug Executed Commands', null )
        ;
    }

    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
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
                ->get( 'vs_app.commands_provider.database_setup' )
                ->getCommands( $input, $output, $this->getHelper( 'question' ) )
            ;
            
            $this->runCommands( $commands, $output );
            $outputStyle->newLine();
        }
        
        return Command::SUCCESS;
    }
}
