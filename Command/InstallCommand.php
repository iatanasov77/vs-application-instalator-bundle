<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\RuntimeException;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;

final class InstallCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'vankosoft:install';
    
    /**
     * @var array
     *
     * @psalm-var non-empty-list
     */
    private $commands = [
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
            'command' => 'assets',
            'message' => 'Installing assets.',
        ],
        
        //         For Now Sample Data is In Construction and is Not Available
        //         [
        //             'command' => 'sample-data',
        //             'message' => 'Install Application Simple Data.',
        //         ],
    ];
    
    protected function configure(): void
    {
        $this
            ->setDescription( 'Installs VankoSoft Application in your preferred environment.' )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs VankoSoft Application.
EOT
            )
            ->addOption( 'fixture-suite', 's', InputOption::VALUE_OPTIONAL, 'Load specified fixture suite during install', null )
            ->addOption( 'debug-commands', 'd', InputOption::VALUE_OPTIONAL, 'Debug Executed Commands', null )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $suite = $input->getOption( 'fixture-suite' );
        $debug = $input->getOption( 'debug-commands' );
        
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( '<info>Installing VankoSoft Application...</info>' );
        //$outputStyle->writeln( $this->getSyliusLogo() );
        $outputStyle->writeln( $this->getVankoSoftLogo() );
        
        $this->ensureDirectoryExistsAndIsWritable( (string) $this->getContainer()->getParameter( 'kernel.cache_dir' ), $output );
        
        $errored        = false;
        $defaultLocale  = null;
        foreach ( $this->commands as $step => $command ) {
            try {
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
                        if ( $suite )
                            $parameters['--fixture-suite']  = $suite;
                        if ( $debug )
                            $parameters['--debug-commands'] = $debug;
                        break;
                    case 'application-configuration':
                        // Database is already Installed. Setup Default Locale.
                        $defaultLocale  = $this->getContainer()->get( 'vs_app.setup.locale' )->setup( $input, $output, $this->getHelper( 'question' ) );
                        break;
                    case 'setup-super-admin-application':
                        $parameters['--default-locale']  = $defaultLocale ? $defaultLocale->getCode() : null;
                        break;
                    case 'setup-applications':
                        $parameters['--default-locale']  = $defaultLocale ? $defaultLocale->getCode() : null;
                        break;
                    case 'sample-data':
                        $parameters['--fixture-suite']  = 'vankosoft_sampledata_suite';
                        break;
                }
                
                $this->commandExecutor->runCommand( 'vankosoft:install:' . $command['command'], $parameters, $output );
                
            } catch ( RuntimeException $exception ) {
                $errored = true;
            }
        }
        $this->commandExecutor->runCommand( 'liip:imagine:cache:remove', [], $output ); // Clear Liip Imagine Cache
        
        $outputStyle->newLine( 2 );
        $outputStyle->success( $this->getProperFinalMessage( $errored ) );
        $outputStyle->writeln( 'Configure your application document root at public/{application-name} and admin panel at public/admin-panel .' );
        
        return $errored ? 1 : 0;
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
