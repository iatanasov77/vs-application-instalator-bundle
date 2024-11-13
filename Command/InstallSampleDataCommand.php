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
    name: 'vankosoft:install:sample-data',
    description: 'Install sample data into VankoSoft Application.',
    hidden: false
)]
final class InstallSampleDataCommand extends AbstractInstallCommand
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
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper( 'question' );
        $suite          = $input->getOption( 'fixture-suite' );
        $outputStyle    = new SymfonyStyle( $input, $output );
        
        if ( $suite ) {
            return $this->installApplicationSampleData( $input, $output, $suite );
        }
        
        if ( $this->isCatalogProject() || $this->isExtendedProject() ) {
            if ( $questionHelper->ask( $input, $output, new ConfirmationQuestion( 'Do you want to load sample data? (y/N) ', false ) ) ) {
                return $this->installApplicationSampleData( $input, $output, 'vankosoft_catalog_sample_data_suite' );
                //return $this->loadExtendedSampleData( $input, $output );
            } else {
                $outputStyle->writeln( 'Cancelled loading sample data.' );
            }
        }
        
        return Command::SUCCESS;
    }
    
    private function installApplicationSampleData( InputInterface $input, OutputInterface $output, $suite ): int
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper( 'question' );

        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->newLine();
        $outputStyle->writeln( sprintf(
            'Loading sample data for environment <info>%s</info> from suite <info>%s</info>.',
            $this->getEnvironment(),
            $suite ?? 'vankosoft_sampledata_suite'
        ) );

        try {
            $publicDir = $this->getParameter( 'vs_application.public_dir' );

            $this->ensureDirectoryExistsAndIsWritable( $publicDir . '/media/', $output );
            $this->ensureDirectoryExistsAndIsWritable( $publicDir . '/media/cache/', $output );
        } catch ( \RuntimeException $exception ) {
            $outputStyle->writeln( $exception->getMessage() );

            return Command::FAILURE;
        }
        
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
    
    private function loadExtendedSampleData( InputInterface $input, OutputInterface $output ): int
    {
        $outputStyle        = new SymfonyStyle( $input, $output );
        
        $parameters         = [];
        $this->commandExecutor->runCommand( 'vankosoft:install:extended-sample-data', $parameters, $output );
        
        $outputStyle->newLine();
        $outputStyle->writeln( '<info>Loading sample data for VankoSoft Extended Project successfully.</info>' );
        $outputStyle->newLine();
        
        return Command::SUCCESS;
    }
}
