<?php namespace VS\ApplicationInstalatorBundle\Component;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use VS\ApplicationBundle\Component\Slug;

class SetupApplication
{
    /**
     * @var ContainerInterface $container
     */
    private $container;
    
    /**
     * @var string $applicationSlug
     */
    private $applicationSlug;
    
    /**
     * @var string $applicationName
     */
    private $applicationName;
    
    /**
     * @var string $applicationNamespace
     */
    private $applicationNamespace;
    
    /**
     * @var string $applicationVersion
     */
    private $applicationVersion;
    
    /**
     * @var boolean $setupKernel
     */
    private $setupKernel;
    
    public function __construct( ContainerInterface $container )
    {
        $this->container    = $container;
    }
    
    public function getApplicationDirectories( $applicationName )
    {
        $this->applicationName      = $applicationName;
        $this->applicationNamespace = preg_replace( '/\s+/', '', $applicationName );
        $this->applicationSlug      = Slug::generate( $applicationName ); // For Directory Names
        
        $projectRootDir             = $this->container->get( 'kernel' )->getProjectDir();
        
        if ( $this->setupKernel && $filesystem->exists( $projectRootDir . '/VERSION' ) ) {
            $this->applicationVersion   = file_get_contents( $projectRootDir . '/VERSION' );
            $filesystem->remove( $projectRootDir . '/VERSION' );
        }
        
        $applicationDirs            = [
            'configs'       => $projectRootDir . '/config/applications/' . $this->applicationSlug,
            'public'        => $projectRootDir . '/public/' . $this->applicationSlug,
            'templates'     => $projectRootDir . '/templates/' . $this->applicationSlug,
            'assets'        => $projectRootDir . '/assets/' . $this->applicationSlug,
            'controller'    => $projectRootDir . '/src/Controller/' . $this->applicationNamespace,
        ];
        
        return $applicationDirs;
    }
    
    /**
     * This is the Entry Point of this class
     * 
     * @param string $applicationName
     * @param boolean $setupKernel
     */
    public function setupApplication( $applicationName, $setupKernel = false )
    {
        $this->setupKernel  = $setupKernel;
        $applicationDirs    = $this->getApplicationDirectories( $applicationName );
        
        // Setup The Application
        $this->setupApplicationDirectories( $applicationDirs  );
        $this->setupAdminPanelKernel();
        
        if ( $setupKernel ) {
            $this->setupApplicationKernel();
        }
        
        $this->setupApplicationHomePage();
        $this->setupApplicationLoginPage();
        $this->setupApplicationConfigs();
        $this->setupApplicationRoutes();
        $this->setupApplicationAssets();
        $this->setupInstalationInfo();
    }
    
    private function setupApplicationDirectories( $applicationDirs ): void
    {
        $zip                = new \ZipArchive;
        
        try {
            foreach ( $applicationDirs as $key => $dir ) {                
                try {
                    $dirArchive = $this->container->get( 'kernel' )
                                        ->locateResource( '@VSApplicationInstalatorBundle/Resources/application/' . $key . '.zip' );
                    
                    $res = $zip->open( $dirArchive );
                    if ( $res === TRUE ) {
                        $zip->extractTo( $dir );
                        $zip->close();
                    }
                } catch ( \InvalidArgumentException $e ) {
                    // Kernel::locateResource throws \InvalidArgumentException
                    // if the file cannot be found or the name is not valid
                    
                }
            }
        } catch ( IOExceptionInterface $exception ) {
            echo "An error occurred while creating your directory at " . $exception->getPath();
        }
    }
    
    private function setupAdminPanelKernel()
    {
        $filesystem         = new Filesystem();
        $projectRootDir     = $this->container->get( 'kernel' )->getProjectDir();
        $reflectionClass    = new \ReflectionClass( \App\AdminPanelKernel::class );
        $constants          = $reflectionClass->getConstants();
        
        $filesystem->dumpFile( $projectRootDir . '/src/AdminPanelKernel.php', str_replace(
            $constants['VERSION'], 
            $this->applicationVersion,
            file_get_contents( $projectRootDir . '/src/AdminPanelKernel.php' )
        ));
    }
    
    private function setupApplicationKernel()
    {
        $filesystem         = new Filesystem();
        $projectRootDir     = $this->container->get( 'kernel' )->getProjectDir();
        $twig               = $this->container->get( 'twig' );
        $kernelClass        = $this->applicationNamespace . 'Kernel';
        
        // Write Application Kernel
        $applicationKernel  = $twig->render( '@VSApplicationInstalator/Application/Kernel.php.twig', [
            'kernelClass'           => $kernelClass,
            'applicationSlug'       => $this->applicationSlug,
            'applicationVersion'    => $this->applicationVersion,
        ]);
        $filesystem->dumpFile( $projectRootDir . '/src/' . $kernelClass . '.php', $applicationKernel );
        
        // Write Application Entry Point
        $applicationIndex  = $twig->render( '@VSApplicationInstalator/Application/index.php.twig', [
            'kernelClass'       => $kernelClass,
        ]);
        $filesystem->dumpFile( $projectRootDir . '/public/' . $this->applicationSlug . '/index.php', $applicationIndex );
        
        // Write Application Console
        $applicationConsole = $twig->render( '@VSApplicationInstalator/Application/console.php.twig', [
            'kernelClass'       => $kernelClass,
        ]);
        $filesystem->dumpFile( $projectRootDir . '/bin/' . $this->applicationSlug, $applicationConsole );
        
        // Remove original Kernel
        $filesystem->remove( $projectRootDir . '/src/Kernel.php' );
        $filesystem->remove( $projectRootDir . '/public/index.php' );
    }
    
    private function setupApplicationHomePage()
    {
        $filesystem             = new Filesystem();
        $projectRootDir         = $this->container->get( 'kernel' )->getProjectDir();
        
        // Write Application Home Page
        $applicationHomePage    = str_replace(
                                        "__application_slug__", $this->applicationSlug,
                                        file_get_contents( $projectRootDir . '/templates/' . $this->applicationSlug . '/pages/Dashboard/index.html.twig' )
                                    );
        $filesystem->dumpFile( $projectRootDir . '/templates/' . $this->applicationSlug . '/pages/Dashboard/index.html.twig', $applicationHomePage );
        
        // Write Application Home Controller
        $applicationHomeController  = str_replace(
                                        ["__application_name__", "__application_slug__"],
                                        [$this->applicationNamespace, $this->applicationSlug],
                                        file_get_contents( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/DefaultController.php' )
                                    );
        $filesystem->dumpFile( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/DefaultController.php', $applicationHomeController );
    }
    
    private function setupApplicationConfigs()
    {
        $filesystem     = new Filesystem();
        $projectRootDir = $this->container->get( 'kernel' )->getProjectDir();
        
        // Setup Cache Preloader
        $configPreload  = str_replace(
            ["__application_slug__"],
            [$this->applicationSlug],
            file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/preload.php' )
        );
        $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/preload.php', $configPreload );
        
        // Setup Services and Parameters
        $configServices = str_replace(
                            ["__application_name__", "__application_slug__", "__kernel_class__"],
                            [$this->applicationName, $this->applicationSlug, $this->applicationNamespace . 'Kernel'],
                            file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/services.yaml' )
        );
        $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/services.yaml', $configServices );
    }
    
    private function setupApplicationAssets()
    {
        $filesystem     = new Filesystem();
        $projectRootDir = $this->container->get( 'kernel' )->getProjectDir();
        
        // Setup Webpack Encore
        $configWebpackEncore    = str_replace(
            ["__application_slug__"],
            [$this->applicationSlug],
            file_get_contents( $projectRootDir . '/webpack.config.js' )
        );
        $filesystem->dumpFile( $projectRootDir . '/webpack.config.js', $configWebpackEncore );
        
        $configWebpackEncore    = str_replace(
            ["__application_slug__"],
            [$this->applicationSlug],
            file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/packages/webpack_encore.yaml' )
        );
        $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/packages/webpack_encore.yaml', $configWebpackEncore );
    }
    
    private function setupApplicationLoginPage()
    {
        $filesystem             = new Filesystem();
        $projectRootDir         = $this->container->get( 'kernel' )->getProjectDir();
        
        // Write Application Login Page
        $applicationLoginPage    = str_replace(
            "__application_slug__", $this->applicationSlug,
            file_get_contents( $projectRootDir . '/templates/' . $this->applicationSlug . '/pages/login.html.twig' )
        );
        $filesystem->dumpFile( $projectRootDir . '/templates/' . $this->applicationSlug . '/pages/login.html.twig', $applicationLoginPage );
        
        // Write Application Home Controller
        $applicationAuthController  = str_replace(
                                        ["__application_name__", "__application_slug__"],
                                        [$this->applicationNamespace, $this->applicationSlug],
                                        file_get_contents( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/AuthController.php' )
                                    );
        $filesystem->dumpFile( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/AuthController.php', $applicationAuthController );
    }
    
    private function setupApplicationRoutes()
    {
        $filesystem             = new Filesystem();
        $projectRootDir         = $this->container->get( 'kernel' )->getProjectDir();
        
        $configRoutes   = str_replace(
            ["__application_name__"],
            [$this->applicationNamespace],
            file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes.yaml' )
        );
        $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes.yaml', $configRoutes );
        
        $configRoutes   = str_replace(
            ["__application_name__"],
            [$this->applicationNamespace],
            file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/vs_users.yaml' )
            );
        $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/vs_users.yaml', $configRoutes );
    }
    
    private function setupInstalationInfo()
    {
        $filesystem         = new Filesystem();
        $projectRootDir     = $this->container->get( 'kernel' )->getProjectDir();
        
        
    }
}
