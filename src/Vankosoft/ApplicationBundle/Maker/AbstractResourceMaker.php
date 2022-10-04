<?php namespace Vankosoft\ApplicationBundle\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Doctrine\EntityDetails;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

use App\Maker\Renderer\FormTypeRenderer;

use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use Vankosoft\ApplicationBundle\Repository\ApplicationRepositoryInterface;
use Vankosoft\ApplicationBundle\Repository\SettingsRepositoryInterface;

/*
 * Good Tutorials:
 *      https://akashicseer.com/web-development/symfony-5-how-to-create-a-maker/
 *
 * Used for Example: Symfony\Bundle\MakerBundle\Maker\MakeCrud
 */
abstract class AbstractResourceMaker extends AbstractMaker
{
    protected ApplicationInterface $application;
    protected string $applicationConfigPath;
    
    protected string $resourceName;
    protected string $resourceId;
    protected string $resourceRoute;
    protected bool $applicationHasTheme;
    protected string $makerTemplatesPath;
    protected string $templatesPath;
    
    protected $doctrineHelper;
    protected $formTypeRenderer;
    protected $inflector;
    protected $controllerClassName;
    protected $generateTests = false;
    
    protected ApplicationRepositoryInterface $applicationRepository;
    protected SettingsRepositoryInterface $settingsRepository;
    
    public function __construct(
        KernelInterface $kernel,
        DoctrineHelper $doctrineHelper,
        FormTypeRenderer $formTypeRenderer,
        ApplicationRepositoryInterface $applicationRepository,
        SettingsRepositoryInterface $settingsRepository
    ) {
        $this->makerTemplatesPath       = $kernel->locateResource( '@VSApplicationBundle/Resources/maker' );
        
        $this->doctrineHelper           = $doctrineHelper;
        $this->formTypeRenderer         = $formTypeRenderer;
        $this->inflector                = InflectorFactory::create()->build();
        
        $this->applicationRepository    = $applicationRepository;
        $this->settingsRepository       = $settingsRepository;
    }
    
    /**
     * Configure any library dependencies that your maker requires.
     * @param DependencyBuilder $dependencies
     */
    public function configureDependencies( DependencyBuilder $dependencies )
    {
        // TODO: Implement configureDependencies() method.
    }
    
    /**
     * Called after normal code generation: allows you to do anything.
     *
     * @param InputInterface $input
     * @param ConsoleStyle $io
     * @param Generator $generator
     */
    public function generate( InputInterface $input, ConsoleStyle $io, Generator $generator )
    {
        $this->resourceName         = $input->getArgument( 'name' );
        $this->resourceId           = 'vsapp.' . strtolower( $this->inflector->pluralize( $this->resourceName ) );
        $this->resourceRoute        = 'vsapp_' . strtolower( $this->inflector->pluralize( $this->resourceName ) );
        
        $this->application  = $this->applicationRepository->findOneByCode( $input->getOption( 'application' ) );
        if ( ! $this->application ) {
            throw new \Exception( 'Application Not Found !' );
        }
        
        if ( $this->application->getCode() == 'admin-panel' ) {
            $this->applicationConfigPath    = $generator->getRootDirectory() . '/config/admin-panel';
        } else {
            $this->applicationConfigPath    = $generator->getRootDirectory() . '/config/applications/' . $this->application->getCode();
        }
        
        $settings   = $this->settingsRepository->getSettings( $this->application->getId() );
        if( $settings && $settings->getTheme() ) {
            $this->templatesPath        = $settings->getTheme()->getPath() . '/templates/Pages/' . $this->inflector->pluralize( $this->resourceName );
            $this->applicationHasTheme  = true;
        } else {
            $this->templatesPath        = $generator->getRootDirectory() . '/templates/' .  $this->application->getCode() . '/Pages/' . $this->inflector->pluralize( $this->resourceName );
            $this->applicationHasTheme  = false;
        }
        
        $entityClassDetails = $generator->createClassNameDetails(
            $this->resourceName,
            'Entity\\'
        );
        
        if (
            ! $this->doctrineHelper->isDoctrineSupportingAttributes() &&
            $this->doctrineHelper->doesClassUsesAttributes( $entityClassDetails->getFullName() )
        ) {
            throw new RuntimeCommandException( 'To use Doctrine entity attributes you\'ll need PHP 8, doctrine/orm 2.9, doctrine/doctrine-bundle 2.4 and symfony/framework-bundle 5.2.' );
        }
        
        if ( ! \class_exists( $entityClassDetails->getFullName() ) ) {
            throw new \Exception( 'Entity Class Not Found !' );
        }
        
        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails( $entityClassDetails->getFullName() );
        
        /*
         * Generate Resources
         */
        $formClassDetails       = $this->generateForm( $io, $generator, $entityClassDetails, $entityDoctrineDetails );
        $controllerClassDetails = $this->generateController( $io, $generator, $entityClassDetails );
        $this->generateAssets( $io, $generator, $entityClassDetails );
        $this->generateTemplates( $io, $generator, $entityClassDetails, $entityDoctrineDetails );
        $this->generateConfigs( $io, $generator, $entityClassDetails, $controllerClassDetails, $formClassDetails );
    }
    
    public function interact( InputInterface $input, ConsoleStyle $io, Command $command )
    {
        $io->title( 'Create the CRUD Resource classes...' );
        
        $entity  = '';
        if ( null === $input->getArgument( 'name' ) ) {
            $argument   = $command->getDefinition()->getArgument( 'name' );
            $question   = new Question( $argument->getDescription() );
            $entity = $io->askQuestion( $question );
            $input->setArgument( 'name', $entity );
            
        }
        
        $entity  = $input->getArgument( 'name' );
        $io->info( "Resource CRUD classes will be made for entity: " . $entity );
    }
    
    abstract protected function generateForm( ConsoleStyle $io, Generator $generator, ClassNameDetails $entityClassDetails, EntityDetails $entityDoctrineDetails );
    abstract protected function generateController( ConsoleStyle $io, Generator $generator, ClassNameDetails $entityClassDetails );
    abstract protected function generateAssets( ConsoleStyle $io, Generator $generator, ClassNameDetails $entityClassDetails );
    abstract protected function generateTemplates( ConsoleStyle $io, Generator $generator, ClassNameDetails $entityClassDetails, EntityDetails $entityDoctrineDetails );
    
    protected function generateConfigs(
        ConsoleStyle $io,
        Generator $generator,
        ClassNameDetails $entityClassDetails,
        ClassNameDetails $controllerClassDetails,
        ClassNameDetails $formClassDetails
    ) {
        $io->success( "Now generating Resource Configs." );
        
        $filesystem = new Filesystem();
        
        $templates  = [
            'resource'              => [
                'target_file'       => $this->applicationConfigPath . '/packages/sylius_resource.yaml',
                'resource_id'       => $this->resourceId,
                'model_class'       => $entityClassDetails->getFullName(),
                'controller_class'  => $controllerClassDetails->getFullName(),
                'form_class'        => $formClassDetails->getFullName(),
            ],
            'route_resource'        => [
                'target_file'           => $this->applicationConfigPath . '/routes/sylius_resource.yaml',
                'route_id'              => $this->resourceRoute,
                'route_alias'           => $this->resourceId,
                'route_path'            => '/' . strtolower( $this->inflector->pluralize( $this->resourceName ) ),
                'route_templates_path'  => explode( '/templates/', $this->templatesPath )[1],
            ],
            //             'service_controller'    => [
                //                 'target_file'       => $this->applicationConfigPath . '/services/controller.yaml',
                //                 'controller_class'  => $controllerClassDetails->getFullName(),
                //             ],
            'service_form'          => [
                'target_file'   => $this->applicationConfigPath . '/services/form.yaml',
                'service_id'    => 'vsapp.form.' . strtolower( $this->inflector->pluralize( $this->resourceName ) ),
                'resource_name' => strtolower( $this->inflector->pluralize( $this->resourceName ) ),
                'form_class'    => $formClassDetails->getFullName(),
            ],
        ];
        
        $templatePath   = $this->makerTemplatesPath . '/resource/config/';
        foreach ( $templates as $template => $variables ) {
            if ( $filesystem->exists( $variables['target_file'] ) ) {
                $configArray    = Yaml::parseFile( $variables['target_file'] );
            } else {
                $configArray    = [];
                $filesystem->touch( $variables['target_file'] );
            }
            
            switch ( $template ) {
                case 'resource':
                    $resourceConfig = [
                        'driver'    => 'doctrine/orm',
                        'classes'   => [
                            'model'         => $variables['model_class'],
                            'interface'     => 'Sylius\Component\Resource\Model\ResourceInterface',
                            'controller'    => $variables['controller_class'],
                            'repository'    => 'Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository',
                            'form'          => $variables['form_class']
                        ]
                    ];
                    $configArray['sylius_resource']['resources'][$variables['resource_id']] = $resourceConfig;
                    $configString   = Yaml::dump( $configArray, 5 );
                    
                    break;
                case 'route_resource':
                    $configString   .= \file_get_contents( $variables['target_file'] ) . PHP_EOL . PHP_EOL .
                    $this->parseTemplate( $templatePath . $template . '.tpl.php' , $variables );
                    
                    break;
                case 'service_controller':
                    
                    break;
                case 'service_form':
                    $serviceConfig = [
                        'public'    => true,
                        'class'     => $variables['form_class'],
                        'arguments' => [
                            "%vsapp.model.{$variables['resource_name']}.class%",
                            "@request_stack"
                        ],
                        'tags'      => ['form.type']
                    ];
                    $configArray['services'][$variables['service_id']] = $serviceConfig;
                    $configString   = Yaml::dump( $configArray, 5 );
                    
                    break;
                default:
                    throw new \Exception( 'Runtime Exception !!!' );
                    
            }
            
            $filesystem->dumpFile( $variables['target_file'], $configString );
        }
    }
    
    protected function parseTemplate( string $templatePath, array $parameters ): string
    {
        ob_start();
        extract( $parameters, \EXTR_SKIP );
        include $templatePath;
        
        return ob_get_clean();
    }
}
