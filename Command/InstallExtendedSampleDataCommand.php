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
    name: 'vankosoft:install:extended-sample-data',
    description: 'Install extended sample data into VankoSoft Extended Project.',
    hidden: false
)]
final class InstallExtendedSampleDataCommand extends AbstractInstallCommand
{
    protected function configure(): void
    {
        $this
            ->setHelp(<<<EOT
The <info>%command.name%</info> command loads the sample data for VankoSoft Extended Project.
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
        $outputStyle->newLine();
        $outputStyle->writeln( sprintf(
            'Loading sample data into VankoSoft Extended Project for environment <info>%s</info> from suite <info>%s</info>.',
            $this->getEnvironment(),
            $suite ?? 'vankosoft_catalog_sample_data_suite'
        ) );

        $parameters = [
            'suite' => $suite ?: 'vankosoft_catalog_sample_data_suite',
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
