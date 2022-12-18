<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckRequirementsCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'vankosoft:install:check-requirements';

    protected function configure(): void
    {
        $this
            ->setDescription( 'Checks if all VankoSoft Application requirements are satisfied.' )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command checks system requirements.
EOT
            )
        ;
    }

    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $fulfilled = $this->get( 'vs_app.installer.checker.application_requirements' )->check( $input, $output );

        if ( ! $fulfilled ) {
            throw new RuntimeException(
                'Some system requirements are not fulfilled. Please check output messages and fix them.'
            );
        }

        $output->writeln( '<info>Success! Your system can run VankoSoft Application properly.</info>' );

        return 0;
    }
}
