<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;

use Vankosoft\ApplicationBundle\Command\ContainerAwareCommand;

final class SetupFinalizeCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'vankosoft:install:finalize-setup';
    
    protected function configure(): void
    {
        $this
            ->setDescription( 'Finalize VankoSoft Application Installation.' )
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
        
        return 0;
    }
}
