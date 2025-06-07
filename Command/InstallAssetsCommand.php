<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'vankosoft:install:assets',
    description: 'Installs all VankoSoft Application assets.',
    hidden: false
)]
final class InstallAssetsCommand extends AbstractInstallCommand
{
    protected function configure(): void
    {
        $this
            ->setHelp(<<<EOT
The <info>%command.name%</info> command downloads and installs all VankoSoft Application media assets.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln( sprintf(
            'Installing VankoSoft Application assets for environment <info>%s</info>.',
            $this->getEnvironment()
        ) );

        $publicDir = $this->getParameter( 'vs_application.public_dir' );
        
        try {
            $this->ensureDirectoryExistsAndIsWritable( $publicDir . '/assets/', $output );
            $this->ensureDirectoryExistsAndIsWritable( $publicDir . '/bundles/', $output );
        } catch ( \RuntimeException $exception ) {
            $output->writeln( $exception->getMessage() );

            return Command::FAILURE;
        }

        $commands = [
            'assets:install' => ['target' => $publicDir],
        ];

        $this->runCommands( $commands, $output );

        return Command::SUCCESS;
    }
}
