<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'vankosoft:install:application-configuration',
    description: 'Install sample data into VankoSoft Application.',
    hidden: false
)]
final class InstallApplicationConfigurationCommand extends AbstractInstallCommand
{
    protected function configure(): void
    {
        $this
            ->setHelp(<<<EOT
The <info>%command.name%</info> command loads the sample data for VankoSoft Application.
EOT
            )
            ->addOption( 'fixture-suite', 's', InputOption::VALUE_OPTIONAL, 'Load specified fixture suite during install', null )
        ;
    }

    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $suite          = $input->getOption( 'fixture-suite' );
        
        if ( $suite ) {
            return $this->installApplicationConfiguration( $input, $output, $suite );
        } else {
            $return = $this->installApplicationConfiguration( $input, $output, 'vankosoft_application_configuration_suite' );
            if ( $this->isCatalogProject() || $this->isExtendedProject() ) {
                $return = $this->installApplicationConfiguration( $input, $output, 'vankosoft_catalog_configuration_suite' );
            }
            
            return $return;
        }
    }
    
    private function installApplicationConfiguration( InputInterface $input, OutputInterface $output, $suite ): int
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->newLine();
        
        $outputStyle->writeln( sprintf(
            'Loading application configuration data for environment <info>%s</info> from suite <info>%s</info>.',
            $this->getEnvironment(),
            $suite
        ) );
        //$outputStyle->writeln( '<error>Warning! This action will erase your database.</error>' );
        
        $parameters = [
            'suite' => $suite,
            '--no-interaction' => true,
        ];
        
        $commands = [
            'sylius:fixtures:load' => $parameters,
        ];
        
        $this->runCommands( $commands, $output );
        $outputStyle->newLine( 2 );
        
        return Command::SUCCESS;
    }
}
