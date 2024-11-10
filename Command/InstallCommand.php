<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\RuntimeException;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;

#[AsCommand(
    name: 'vankosoft:install',
    description: 'Installs VankoSoft Application in your preferred environment.',
    hidden: false
)]
final class InstallCommand extends AbstractInstallCommand
{
    private $defaultLocale          = null;
    
    /**
     * @var array
     *
     * @psalm-var non-empty-list
     */
    private $commands               = [
        [
            'command' => 'check-requirements',
            'message' => 'Checking system requirements.',
        ],
        [
            'command' => 'database',
            'message' => 'Setting up the database.',
        ],
        [
            'command' => 'application-configuration',
            'message' => 'Load General Configuration.',
        ],
        [
            'command' => 'setup-super-admin-application',
            'message' => 'Setup SuperAdmin Panel.',
        ],
        [
            'command' => 'setup-applications',
            'message' => 'Setup Main Application Layout.',
        ],
        
        [
            'command' => 'sample-data',
            'message' => 'Install Application Simple Data.',
        ],
        
        // I think this Command Is Not Needed Anymore
        //
        // [
        //     'command' => 'assets',
        //     'message' => 'Installing assets.',
        // ],
        
        [
            'command' => 'finalize-setup',
            'message' => 'Finalize VankoSoft Application Setup.',
        ],
    ];
    
    protected function configure(): void
    {
        $this
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs VankoSoft Application.
EOT
            )
            ->addOption( 'debug-commands', 'd', InputOption::VALUE_OPTIONAL, 'Debug Executed Commands', null )
            ->addOption( 'app-config-fixture-suite', 'c', InputOption::VALUE_OPTIONAL, 'Load specified fixture suite during install', null )
            ->addOption( 'sample-data-fixture-suite', 's', InputOption::VALUE_OPTIONAL, 'Load specified fixture suite during install', null )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( '<info>Installing VankoSoft Application...</info>' );
        //$outputStyle->writeln( $this->getSyliusLogo() );
        $outputStyle->writeln( $this->getVankoSoftLogo() );
        
        $this->ensureDirectoryExistsAndIsWritable( (string) $this->getParameter( 'kernel.cache_dir' ), $output );
        
        $errored        = false;
        try {
            $this->executeCommands( $input, $output );
            $this->loadWidgets();
        } catch ( RuntimeException $exception ) {
            $errored = true;
        }
        $this->commandExecutor->runCommand( 'liip:imagine:cache:remove', [], $output ); // Clear Liip Imagine Cache
        
        $outputStyle->newLine( 2 );
        $outputStyle->success( $this->getProperFinalMessage( $errored ) );
        $outputStyle->writeln( 'Configure your application document root at public/{application-name} and admin panel at public/admin-panel .' );
        
        return $errored ? Command::FAILURE : Command::SUCCESS;
    }
    
    private function executeCommands( InputInterface $input, OutputInterface $output )
    {
        $debug              = $input->getOption( 'debug-commands' );
        $appConfigSuite     = $input->getOption( 'app-config-fixture-suite' );
        $sampleDataSuite    = $input->getOption( 'sample-data-fixture-suite' );
        
        $outputStyle        = new SymfonyStyle( $input, $output );
        
        foreach ( $this->commands as $step => $command ) {
            
            $outputStyle->newLine();
            $outputStyle->section( sprintf(
                'Step %d of %d. <info>%s</info>',
                $step + 1,
                count( $this->commands ),
                $command['message']
            ));
            
            $parameters = [];
            switch ( $command['command'] ) {
                case 'database':
                    if ( $debug )
                        $parameters['--debug-commands'] = $debug;
                    
                     break;
                case 'application-configuration':
                    // Database is already Installed. Setup Default Locale.
                    $this->defaultLocale  = $this->get( 'vs_app.setup.locale' )->setup( $input, $output, $this->getHelper( 'question' ) );
                    
                    if ( $appConfigSuite ) {
                        $parameters['--fixture-suite']  = $appConfigSuite;
                    }
                    
                    break;
                case 'setup-super-admin-application':
                    $parameters['--default-locale']  = $this->defaultLocale ? $this->defaultLocale->getCode() : null;
                    
                    break;
                case 'setup-applications':
                    $parameters['--default-locale']  = $this->defaultLocale ? $this->defaultLocale->getCode() : null;
                    
                    break;
                case 'sample-data':
                    if ( $sampleDataSuite ) {
                        $parameters['--fixture-suite']  = $sampleDataSuite;
                    }
                    
                    break;
            }
            
            $this->commandExecutor->runCommand( 'vankosoft:install:' . $command['command'], $parameters, $output );
        }
    }
    
    private function loadWidgets(): void
    {
        $widgetsContainer   = $this->get( 'vs_application.widgets_container' );
        
        $users  = $this->get( 'vs_users.repository.users' )->findAll();
        foreach ( $users as $user ) {
            $widgetsContainer->loadWidgets( $user, false, true );
        }
        $widgetsContainer->loadWidgets( null, false, true );
    }
    
    private function getProperFinalMessage( bool $errored ): string
    {
        if ( $errored ) {
            return 'VankoSoft Application has been installed, but some error occurred.';
        }
        
        return 'VankoSoft Application has been successfully installed.';
    }
    
    /**
     * @EXAMPLE
     */
    private function getSyliusLogo(): string
    {
        return '
           <info>,</info>
         <info>,;:,</info>
       <info>`;;;.:`</info>
      <info>`::;`  :`</info>
       <info>:::`   `</info>          .\'++:           \'\'.   \'.
       <info>`:::</info>             :+\',;+\'          :+;  `+.
        <info>::::</info>            +\'   :\'          `+;
        <info>`:::,</info>           \'+`     ++    :+.`+; `++. ;+\'    \'\'  ,++++.
         <info>,:::`</info>          `++\'.   .+:  `+\' `+;  .+,  ;+    +\'  +;  \'\'
          <info>::::`</info>           ,+++.  \'+` :+. `+;  `+,  ;+    +\'  \'+.
   <info>,.     .::::</info>             .++` `+: +\'  `+;  `+,  ;+    +\'  `;++;
<info>`;;.:::`   :::::</info>             :+.  \'+,+.  `+;  `+,  ;+   `+\'     .++
 <info>.;;;;;;::`.::::,</info>       +\'` `++   `++\'   `+;  `+:  :+. `++\'  \'.  ;+
  <info>,;;;;;;;;;:::::</info>       .+++++`    ;+,    ++;  ++, `\'+++,\'+\' :++++,
   <info>,;;;;;;;;;:::</info>`                  ;\'
    <info>:;;;;;;;;;:,</info>                :.:+,
     <info>;;;;;;;;;:</info>                 ;++,'
            ;
    }
    
    private function getVankoSoftLogo(): string
    {
        /*
         * Generated Here: https://www.ascii-art-generator.org/
         */
        return '
#     #                              #####                            #                                                               
#     #   ##   #    # #    #  ####  #     #  ####  ###### #####      # #   #####  #####  #      #  ####    ##   ##### #  ####  #    # 
#     #  #  #  ##   # #   #  #    # #       #    # #        #       #   #  #    # #    # #      # #    #  #  #    #   # #    # ##   # 
#     # #    # # #  # ####   #    #  #####  #    # #####    #      #     # #    # #    # #      # #      #    #   #   # #    # # #  # 
 #   #  ###### #  # # #  #   #    #       # #    # #        #      ####### #####  #####  #      # #      ######   #   # #    # #  # # 
  # #   #    # #   ## #   #  #    # #     # #    # #        #      #     # #      #      #      # #    # #    #   #   # #    # #   ## 
   #    #    # #    # #    #  ####   #####   ####  #        #      #     # #      #      ###### #  ####  #    #   #   #  ####  #    # 

        ';
    }
}
