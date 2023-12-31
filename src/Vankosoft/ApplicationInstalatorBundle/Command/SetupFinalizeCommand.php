<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;

use Vankosoft\ApplicationBundle\Command\ContainerAwareCommand;

#[AsCommand(
    name: 'vankosoft:install:finalize-setup',
    description: 'Finalize VankoSoft Application Installation.',
    hidden: false
)]
final class SetupFinalizeCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this
            ->setHelp(<<<EOT
The <info>%command.name%</info> Command Finalize VankoSoft Application Setup.
EOT
            )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $appSetup           = $this->get( 'vs_application.installer.setup_application' );
        $outputStyle        = new SymfonyStyle( $input, $output );
        
        $appSetup->finalizeSetup();
        
        return Command::SUCCESS;
    }
}
