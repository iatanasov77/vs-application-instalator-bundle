<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InstallAssetsCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'vankosoft:install:assets';

    protected function configure(): void
    {
        $this
            ->setDescription( 'Installs all VankoSoft Application assets.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command downloads and installs all VankoSoft Application media assets.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        
        $output->writeln( sprintf(
            'Installing VankoSoft Application assets for environment <info>%s</info>.',
            $this->getEnvironment()
        ) );

        $publicDir = $this->getContainer()->getParameter( 'vs_application.public_dir' );
        
        try {
            $this->ensureDirectoryExistsAndIsWritable( $publicDir . '/assets/', $output );
            $this->ensureDirectoryExistsAndIsWritable( $publicDir . '/bundles/', $output );
        } catch ( \RuntimeException $exception ) {
            $output->writeln( $exception->getMessage() );

            return 1;
        }

        $commands = [
            'assets:install' => ['target' => $publicDir],
        ];

        $this->runCommands( $commands, $output );
        $outputStyle->newLine( 2 );
        
        return 0;
    }
}
