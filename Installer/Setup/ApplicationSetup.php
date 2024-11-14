<?php namespace Vankosoft\ApplicationInstalatorBundle\Installer\Setup;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Twig\Environment;

use Vankosoft\ApplicationBundle\Component\SlugGenerator;
use Vankosoft\ApplicationBundle\Component\Application\Project;
use Vankosoft\ApplicationInstalatorBundle\Command\AbstractInstallCommand;

class ApplicationSetup
{
    /** @var ContainerInterface $container */
    private $container;
    
    /** @var Environment $twig */
    private $twig;
    
    /** @var string $applicationSlug */
    private $applicationSlug;
    
    /** @var string $applicationName */
    private $applicationName;
    
    /** @var string $applicationNamespace */
    private $applicationNamespace;
    
    /** @var string $applicationVersion */
    private $applicationVersion;
    
    /** @var string $applicationType */
    private $applicationType;
    
    /** @var string $applicationDefaultLocale */
    private $applicationDefaultLocale;
    
    /** @var boolean $newProjectInstall */
    private $newProjectInstall;
    
    /** @var boolean $newProjectInstall */
    private $isExtendedProject;
    
    /** @var SlugGenerator */
    private $slugGenerator;
    
    public function __construct( ContainerInterface $container, Environment $twig, SlugGenerator $slugGenerator )
    {
        $this->container        = $container;
        $this->twig             = $twig;
        $this->slugGenerator    = $slugGenerator;
    }
    
    public function getApplicationDirectories( $applicationName )
    {
        $filesystem                 = new Filesystem();
        $this->applicationName      = $applicationName;
        $this->applicationNamespace = preg_replace( '/\s+/', '', $applicationName );
        $this->applicationSlug      = $this->slugGenerator->generate( $applicationName ); // For Directory Names
        
        $projectRootDir             = $this->container->get( 'kernel' )->getProjectDir();
        
        $applicationDirs            = [
            'configs'       => $projectRootDir . '/config/applications/' . $this->applicationSlug,
            'public'        => $projectRootDir . '/public/' . $this->applicationSlug,
            'controller'    => $projectRootDir . '/src/Controller/' . $this->applicationNamespace,
        ];
        
        
        
        return $applicationDirs;
    }
    
    /**
     * This is the Entry Point of this class
     *
     * @param string $applicationName
     * @param boolean $newProjectInstall
     */
    public function setupApplication( $applicationName, $localeCode, $newProjectInstall = false, $applcationType = null )
    {
        $this->applicationDefaultLocale = $localeCode;
        $this->newProjectInstall        = $newProjectInstall;
        $this->applicationType          = $applcationType;
        $this->_initialize();
        
        $applicationDirs                = $this->getApplicationDirectories( $applicationName );
        
        // Setup The Application
        $this->setupApplicationDirectories( $applicationDirs );
        
        $this->setupApplicationKernel();
        $this->setupApplicationConfigs();
        
        if ( $this->applicationType !== AbstractInstallCommand::APPLICATION_TYPE_API ) {
            $this->setupApplicationHomePage();
            $this->setupApplicationLoginPage();
            $this->setupApplicationStandardPages();
        }
        
        if ( $this->isCatalogProject() ) {
            $this->setupApplicationCatalogPages();
        }
        
        if ( $this->isExtendedProject() ) {
            switch ( $this->applicationType ) {
                case AbstractInstallCommand::APPLICATION_TYPE_CATALOG:
                    $this->setupApplicationCatalogPages();
                    break;
                case AbstractInstallCommand::APPLICATION_TYPE_EXTENDED:
                    $this->setupApplicationCatalogPages();
                    $this->setupApplicationExtendedPages();
                    break;
            }
        }
        
        /*
         * This Not Work Properly but MayBe is Not Needed
         * BUT AFTER FIXING _initialize() MAY BE IT SHOULD WORK
         */ 
        //$this->ignoreApplicationControllersInAdminPanelServices();
        
        $this->setupApplicationRoutes();
        $this->setupApplicationAssets();
        $this->setupApplicationThemes();
        $this->setupInstalationInfo();
    }
    
    public function setupAdminPanelKernel()
    {
        $this->newProjectInstall    = true;
        $this->_initialize();
        
        $filesystem         = new Filesystem();
        $projectRootDir     = $this->container->get( 'kernel' )->getProjectDir();
        $reflectionClass    = new \ReflectionClass( \App\AdminPanelKernel::class );
        $constants          = $reflectionClass->getConstants();
        //var_dump( $constants ); die;
        
        $filesystem->dumpFile( $projectRootDir . '/src/AdminPanelKernel.php', str_replace(
            $constants['VERSION'],
            $this->applicationVersion,
            file_get_contents( $projectRootDir . '/src/AdminPanelKernel.php' )
        ));
    }
    
    public function setupAdminPanelDefaultLocale( string $defaultLocale )
    {
        $filesystem     = new Filesystem();
        $projectRootDir = $this->container->get( 'kernel' )->getProjectDir();
        
        $configServices = str_replace(
            [
                "locale: en_US"
            ],
            [
                "locale: " . $defaultLocale
            ],
            file_get_contents( $projectRootDir . '/config/admin-panel/services.yaml' )
        );
        
        $filesystem->dumpFile( $projectRootDir . '/config/admin-panel/services.yaml', $configServices );
    }
    
    public function finalizeSetup()
    {
        $this->removeOriginalKernelConfigs();
    }
    
    public function getApplicationVersion()
    {
        $this->newProjectInstall    = true;
        $this->_initialize();
        
        return $this->applicationVersion;
    }
    
    public function getProjectType(): ?string
    {
        return $this->container->getParameter( 'vs_application.project_type' );
    }
    
    public function isBaseProject(): bool
    {
        return $this->getProjectType() == Project::PROJECT_TYPE_APPLICATION;
    }
    
    public function isCatalogProject(): bool
    {
        return $this->getProjectType() == Project::PROJECT_TYPE_CATALOG;
    }
    
    public function isExtendedProject(): bool
    {
        return $this->getProjectType() == Project::PROJECT_TYPE_EXTENDED;
    }
    
    private function _initialize()
    {
        $filesystem     = new Filesystem();
        $projectRootDir = $this->container->get( 'kernel' )->getProjectDir();
        
        if ( $this->newProjectInstall && $filesystem->exists( $projectRootDir . '/VERSION' ) ) {
            $this->applicationVersion   = file_get_contents( $projectRootDir . '/VERSION' );
            $filesystem->remove( $projectRootDir . '/VERSION' );
        } elseif( ! $this->newProjectInstall ) {
            $this->applicationVersion   = \App\AdminPanelKernel::VERSION;
        }
    }
    
    private function setupApplicationDirectories( $applicationDirs ): void
    {
        $zip            = new \ZipArchive;
        
        switch ( $this->applicationType ) {
            case AbstractInstallCommand::APPLICATION_TYPE_STANDRD:
                $configsRootDir = '@VSApplicationInstalatorBundle/Resources/application/';
                break;
            case AbstractInstallCommand::APPLICATION_TYPE_CATALOG:
                $configsRootDir = '@VSApplicationInstalatorBundle/Resources/application-catalog/';
                break;
            case AbstractInstallCommand::APPLICATION_TYPE_EXTENDED:
                $configsRootDir = '@VSApplicationInstalatorBundle/Resources/application-extended/';
                break;
            case AbstractInstallCommand::APPLICATION_TYPE_API:
                $configsRootDir = '@VSApplicationInstalatorBundle/Resources/application-api/';
                break;
            default:
                throw new SetupException( 'Unknown Application Type !' );
        }
        
        try {
            foreach ( $applicationDirs as $key => $dir ) {
                try {
                    $dirArchive = $this->container->get( 'kernel' )
                                        ->locateResource( $configsRootDir . $key . '.zip' );
                    
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
    
    private function setupApplicationKernel()
    {
        $filesystem         = new Filesystem();
        $projectRootDir     = $this->container->get( 'kernel' )->getProjectDir();
        $kernelClass        = $this->applicationNamespace . 'Kernel';
        
        // Write Application Kernel
        $applicationKernel  = $this->twig->render( '@VSApplicationInstalator/Application/Kernel.php.twig', [
            'kernelClass'           => $kernelClass,
            'applicationSlug'       => $this->applicationSlug,
            'applicationVersion'    => $this->applicationVersion,
        ]);
        $filesystem->dumpFile( $projectRootDir . '/src/' . $kernelClass . '.php', $applicationKernel );
        
        // Write Application Entry Point
        $applicationIndex  = $this->twig->render( '@VSApplicationInstalator/Application/index.php.twig', [
            'kernelClass'       => $kernelClass,
        ]);
        $filesystem->dumpFile( $projectRootDir . '/public/' . $this->applicationSlug . '/index.php', $applicationIndex );
        
        // Write Application Console
        $applicationConsole = $this->twig->render( '@VSApplicationInstalator/Application/console.php.twig', [
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
            [
                "__application_name__",
                "__application_slug__",
                "__kernel_class__",
                "__application_namespace__",
                "__application_locale__"
            ],
            [
                $this->applicationName,
                $this->applicationSlug,
                $this->applicationNamespace . 'Kernel',
                $this->applicationNamespace,
                $this->applicationDefaultLocale
            ],
            file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/services.yaml' )
        );
        $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/services.yaml', $configServices );
        
        // Setup Services and Parameters
        if ( $this->applicationType != AbstractInstallCommand::APPLICATION_TYPE_API ) {
            $configServices = str_replace(
                ["__application_name__", "__application_slug__", "__kernel_class__", "__application_namespace__"],
                [$this->applicationName, $this->applicationSlug, $this->applicationNamespace . 'Kernel', $this->applicationNamespace],
                file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/services/controller.yaml' )
            );
            $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/services/controller.yaml', $configServices );
            
            // Setup Services and Parameters
            $configServices = str_replace(
                ["__application_name__", "__application_slug__", "__kernel_class__", "__application_namespace__"],
                [$this->applicationName, $this->applicationSlug, $this->applicationNamespace . 'Kernel', $this->applicationNamespace],
                file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/services/menu.yaml' )
            );
            $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/services/menu.yaml', $configServices );
            
            // Setup Liip Imagine
            $configLiipImagine  = str_replace(
                ["__application_slug__"],
                [$this->applicationSlug],
                file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/packages/liip_imagine.yaml' )
            );
            $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/packages/liip_imagine.yaml', $configLiipImagine );
        }
        
        if (
            $this->isCatalogProject() || $this->isExtendedProject() &&
            (
                $this->applicationType == AbstractInstallCommand::APPLICATION_TYPE_CATALOG ||
                $this->applicationType == AbstractInstallCommand::APPLICATION_TYPE_EXTENDED ||
                $this->applicationType == AbstractInstallCommand::APPLICATION_TYPE_API
            )
        ) {
            $configServices = str_replace(
                [ "__application_namespace__"],
                [$this->applicationNamespace],
                file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/packages/vs_payment.yaml' )
            );
            $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/packages/vs_payment.yaml', $configServices );
        }
        
        if (
            $this->isExtendedProject() && 
            (
                $this->applicationType == AbstractInstallCommand::APPLICATION_TYPE_EXTENDED ||
                $this->applicationType == AbstractInstallCommand::APPLICATION_TYPE_API
            )
        ) {
            $configServices = str_replace(
                [ "__application_name__"],
                [$this->applicationName],
                file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/packages/vs_api.yaml' )
            );
            $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/packages/vs_api.yaml', $configServices );
        }
    }
    
    private function ignoreApplicationControllersInAdminPanelServices()
    {
        $projectRootDir = $this->container->get( 'kernel' )->getProjectDir();
        $configFile     = $projectRootDir . '/config/admin-panel/services.yaml';
        try {
            // Pass Yaml::PARSE_CONSTANT option Because Yaml !php/const is null
            $yamlArray  = Yaml::parseFile( $configFile, Yaml::PARSE_CONSTANT );
            $yamlArray['services']['App\\']['exclude'][]   =  '../../src/Controller/' . $this->applicationNamespace . '/';
            // https://stackoverflow.com/questions/58547953/symfony-yaml-formatting-the-output
            \file_put_contents( $configFile, Yaml::dump( $yamlArray, 6 ) );
        } catch ( ParseException $exception ) {
            printf( 'Unable to parse the YAML string: %s', $exception->getMessage() );
        }
    }
    
    private function setupApplicationAssets()
    {
        $filesystem     = new Filesystem();
        $projectRootDir = $this->container->get( 'kernel' )->getProjectDir();
        
        $configWebpackEncore    = str_replace(
            ["__application_slug__"],
            [$this->applicationSlug],
            file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/packages/webpack_encore.yaml' )
        );
        $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/packages/webpack_encore.yaml', $configWebpackEncore );
    }
    
    private function setupApplicationThemes()
    {
        $themesRepository   = $this->container->get( 'vs_app.theme_repository' );
        foreach( $themesRepository->findAll() as $theme ) {
            $this->configureApplicationTheme( $theme->getTitle() );
        }
    }
    
    private function configureApplicationTheme( $themeTitle )
    {
        $themeSlug      = $this->slugGenerator->generate( $themeTitle );
        $themeBuildKey  = $this->slugGenerator->generateCamelCase( $themeSlug );
        
        $projectRootDir = $this->container->get( 'kernel' )->getProjectDir();
        $configFile     = $projectRootDir . '/config/applications/' . $this->applicationSlug . '/packages/webpack_encore.yaml';
        try {
            $yamlArray  = Yaml::parseFile( $configFile );
            
            $yamlArray['framework']['assets']['packages'][$themeSlug]['json_manifest_path']
                = '%kernel.project_dir%/public/shared_assets/build/' . $themeSlug . '/manifest.json';
            $yamlArray['webpack_encore']['builds'][$themeBuildKey]
                = '%kernel.project_dir%/public/shared_assets/build/' . $themeSlug;
            
            // https://stackoverflow.com/questions/58547953/symfony-yaml-formatting-the-output
            \file_put_contents( $configFile, Yaml::dump( $yamlArray, 6 ) );
        } catch ( ParseException $exception ) {
            printf( 'Unable to parse the YAML string: %s', $exception->getMessage() );
        }
    }
    
    private function setupApplicationLoginPage()
    {
        $filesystem             = new Filesystem();
        $projectRootDir         = $this->container->get( 'kernel' )->getProjectDir();
        
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
            file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/attributes.yaml' )
        );
        $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/attributes.yaml', $configRoutes );
        
        $configRoutes   = str_replace(
            ["__application_name__"],
            [$this->applicationNamespace],
            file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/vs_application.yaml' )
        );
        $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/vs_application.yaml', $configRoutes );
        
        $configRoutes   = str_replace(
            ["__application_name__"],
            [$this->applicationNamespace],
            file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/vs_users.yaml' )
        );
        $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/vs_users.yaml', $configRoutes );
        
        if ( $this->isCatalogProject() || $this->isExtendedProject() ) {
            $configRoutes   = str_replace(
                ["__application_name__"],
                [$this->applicationNamespace],
                file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/vs_application_extended.yaml' )
            );
            $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/vs_application_extended.yaml', $configRoutes );
            
            $configRoutes   = str_replace(
                ["__application_name__"],
                [$this->applicationNamespace],
                file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/vs_payment.yaml' )
            );
            $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/vs_payment.yaml', $configRoutes );
            
            $configRoutes   = str_replace(
                ["__application_name__"],
                [$this->applicationNamespace],
                file_get_contents( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/vs_catalog.yaml' )
            );
            $filesystem->dumpFile( $projectRootDir . '/config/applications/' . $this->applicationSlug . '/routes/vs_catalog.yaml', $configRoutes );
        }
    }
    
    private function removeOriginalKernelConfigs()
    {
        $projectRootDir         = $this->container->get( 'kernel' )->getProjectDir();
        $filesystem             = new Filesystem();
        $originalKernelConfigs  = [
            $projectRootDir . '/CHANGELOG.md',
            
            // Directories Added by Flex
            $projectRootDir . '/migrations',
            $projectRootDir . '/tests',
            
            // Assets Added by Flex
            $projectRootDir . '/assets/controllers',
            $projectRootDir . '/assets/styles',
            $projectRootDir . '/assets/app.js',
            $projectRootDir . '/assets/bootstrap.js',
            $projectRootDir . '/assets/controllers.json',
            
            // Configs Added by Flex
            $projectRootDir . '/config/packages',
            $projectRootDir . '/config/routes',
            $projectRootDir . '/config/bundles.php',
            $projectRootDir . '/config/preload.php',
            $projectRootDir . '/config/routes.yaml',
            $projectRootDir . '/config/services.yaml',
            
            // Templates Added by Flex
            $projectRootDir . '/templates/base.html.twig',
        ];
        
        foreach( $originalKernelConfigs as $confFile ) {
            $filesystem->remove( $confFile );
        }
    }
    
    private function setupApplicationStandardPages()
    {
        $filesystem             = new Filesystem();
        $projectRootDir         = $this->container->get( 'kernel' )->getProjectDir();
        
        $applicationAuthController  = str_replace(
            ["__application_name__", "__application_slug__"],
            [$this->applicationNamespace, $this->applicationSlug],
            file_get_contents( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/GlobalFormsTrait.php' )
        );
        $filesystem->dumpFile( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/GlobalFormsTrait.php', $applicationAuthController );
        
        $applicationAuthController  = str_replace(
            ["__application_name__", "__application_slug__"],
            [$this->applicationNamespace, $this->applicationSlug],
            file_get_contents( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/PagesController.php' )
        );
        $filesystem->dumpFile( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/PagesController.php', $applicationAuthController );
        
        $applicationAuthController  = str_replace(
            ["__application_name__", "__application_slug__"],
            [$this->applicationNamespace, $this->applicationSlug],
            file_get_contents( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/ContactController.php' )
        );
        $filesystem->dumpFile( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/ContactController.php', $applicationAuthController );
        
        $applicationAuthController  = str_replace(
            ["__application_name__", "__application_slug__"],
            [$this->applicationNamespace, $this->applicationSlug],
            file_get_contents( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/ForgotPasswordController.php' )
        );
        $filesystem->dumpFile( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/ForgotPasswordController.php', $applicationAuthController );
        
        $applicationAuthController  = str_replace(
            ["__application_name__", "__application_slug__"],
            [$this->applicationNamespace, $this->applicationSlug],
            file_get_contents( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/RegisterController.php' )
        );
        $filesystem->dumpFile( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/RegisterController.php', $applicationAuthController );
        
        $applicationAuthController  = str_replace(
            ["__application_name__", "__application_slug__"],
            [$this->applicationNamespace, $this->applicationSlug],
            file_get_contents( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/ProfileController.php' )
        );
        $filesystem->dumpFile( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/ProfileController.php', $applicationAuthController );
    }
    
    private function setupApplicationCatalogPages()
    {
        $filesystem             = new Filesystem();
        $projectRootDir         = $this->container->get( 'kernel' )->getProjectDir();
        
        $applicationAuthController  = str_replace(
            ["__application_name__", "__application_slug__"],
            [$this->applicationNamespace, $this->applicationSlug],
            file_get_contents( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/ProductController.php' )
        );
        $filesystem->dumpFile( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/ProductController.php', $applicationAuthController );
        
        $applicationAuthController  = str_replace(
            ["__application_name__", "__application_slug__"],
            [$this->applicationNamespace, $this->applicationSlug],
            file_get_contents( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/CatalogController.php' )
            );
        $filesystem->dumpFile( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/CatalogController.php', $applicationAuthController );
        
        $applicationAuthController  = str_replace(
            ["__application_name__", "__application_slug__"],
            [$this->applicationNamespace, $this->applicationSlug],
            file_get_contents( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/PricingPlanCheckoutController.php' )
        );
        $filesystem->dumpFile( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/PricingPlanCheckoutController.php', $applicationAuthController );
        
        $applicationAuthController  = str_replace(
            ["__application_name__", "__application_slug__"],
            [$this->applicationNamespace, $this->applicationSlug],
            file_get_contents( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/ShoppingCartCheckoutController.php' )
        );
        $filesystem->dumpFile( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/ShoppingCartCheckoutController.php', $applicationAuthController );
        
        $applicationAuthController  = str_replace(
            ["__application_name__", "__application_slug__"],
            [$this->applicationNamespace, $this->applicationSlug],
            file_get_contents( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/CreditCardController.php' )
        );
        $filesystem->dumpFile( $projectRootDir . '/src/Controller/' . $this->applicationNamespace . '/CreditCardController.php', $applicationAuthController );
    }
    
    private function setupApplicationExtendedPages()
    {
        
    }
    
    private function setupInstalationInfo()
    {
        $filesystem         = new Filesystem();
        $projectRootDir     = $this->container->get( 'kernel' )->getProjectDir();
        
        
    }
}
