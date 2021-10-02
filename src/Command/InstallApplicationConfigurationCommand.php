<?php namespace VS\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InstallApplicationConfigurationCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'vankosoft:install:application-configuration';

    protected function configure(): void
    {
        $this
            ->setDescription( 'Install sample data into VankoSoft Application.' )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command loads the sample data for VankoSoft Application.
EOT
            )
            ->addOption( 'fixture-suite', 's', InputOption::VALUE_OPTIONAL, 'Load specified fixture suite during install', null )
        ;
    }

    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        return $this->installSingleSiteApplicationConfiguration( $input, $output );
        //return $this->installMultiSiteApplicationConfiguration( $input, $output );
    }
    
    private function installSingleSiteApplicationConfiguration( InputInterface $input, OutputInterface $output ): int
    {
        $suite          = $input->getOption( 'fixture-suite' );
        
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->newLine();
        $outputStyle->writeln( sprintf(
            'Loading singlesite application configuration data for environment <info>%s</info> from suite <info>%s</info>.',
            $this->getEnvironment(),
            $suite ?? 'vankosoft_application_suite'
        ) );
        //$outputStyle->writeln( '<error>Warning! This action will erase your database.</error>' );
        
        $parameters = [
            'suite' => $suite ?: 'vankosoft_application_suite',
            '--no-interaction' => true,
        ];
        
        $commands = [
            'sylius:fixtures:load' => $parameters,
        ];
        
        $this->runCommands( $commands, $output );
        $outputStyle->newLine( 2 );
        
        return 0;
    }
    
    private function installMultiSiteApplicationConfiguration( InputInterface $input, OutputInterface $output ): int
    {
        $suite          = $input->getOption( 'fixture-suite' );
        
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->newLine();
        $outputStyle->writeln( sprintf(
            'Loading multisite application configuration data for environment <info>%s</info> from suite <info>%s</info>.',
            $this->getEnvironment(),
            $suite ?? 'vankosoft_multisite_application_suite'
        ) );
        //$outputStyle->writeln( '<error>Warning! This action will erase your database.</error>' );
        
        $parameters = [
            'suite' => $suite ?: 'vankosoft_multisite_application_suite',
            '--no-interaction' => true,
        ];
        
        $commands = [
            'sylius:fixtures:load' => $parameters,
        ];
        
        $this->runCommands( $commands, $output );
        $outputStyle->newLine( 2 );
        
        return 0;
    }
}
