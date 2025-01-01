<?php namespace Vankosoft\ApplicationBundle\Maker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Doctrine\EntityDetails;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Bundle\MakerBundle\Str;

/*
 * Good Tutorials:
 *      https://akashicseer.com/web-development/symfony-5-how-to-create-a-maker/
 *      
 * Used for Example: Symfony\Bundle\MakerBundle\Maker\MakeCrud
 */
final class MakeResourceCrud extends AbstractResourceMaker
{
    /**
     * Return the command name for your maker (e.g. make:report).
     */
    public static function getCommandName(): string
    {
        return 'vankosoft:make:resource';
    }
    
    public static function getCommandDescription(): string
    {
        return '';
    }
    
    /**
     * Configure the command: set description, input arguments, options, etc.
     *
     * By default, all arguments will be asked interactively. If you want
     * to avoid that, use the $inputConfig->setArgumentAsNonInteractive() method.
     * 
     * @param Command $command
     * @param InputConfiguration $inputConfig
     */
    public function configureCommand( Command $command, InputConfiguration $inputConfig )
    {
        $command->setDescription( 'Creates Resource CRUD' )
            ->addArgument( 'name', InputArgument::REQUIRED, 'Class name of the entity from which to create the Resource ' )
            ->addOption( 'exceptRoute', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Which Routes to except in Route Configuration', ['show'] )
            ->addOption( 'application', 'a', InputOption::VALUE_REQUIRED, 'For Which Application to Create Resources ', 'admin-panel' )
        ;
    }
    
    protected function generateForm( InputInterface $input, ConsoleStyle $io, Generator $generator, ClassNameDetails $entityClassDetails, EntityDetails $entityDoctrineDetails )
    {
        $io->success( "Now generating Form Type." );
        
        $formClassDetails   = $generator->createClassNameDetails(
            $entityClassDetails->getRelativeNameWithoutSuffix() . 'Form',
            'Form\\',
            'Form'
        );
        
        if ( $this->application->getCode() == 'admin-panel' ) {
            $this->formTypeRenderer->render(
                $formClassDetails,
                $entityDoctrineDetails->getFormFields(),
                $entityClassDetails,
                [],
                [],
                'vs_project.' . strtolower( $entityClassDetails->getShortName() )
            );
            $generator->writeChanges();
        }
        
        return $formClassDetails;
    }
    
    protected function generateController( InputInterface $input, ConsoleStyle $io, Generator $generator, ClassNameDetails $entityClassDetails )
    {
        $io->success( "Now generating Controller." );
        
        $controllerClassDetails     = $generator->createClassNameDetails(
            $entityClassDetails->getRelativeNameWithoutSuffix() . 'Controller',
            'Controller\\' . preg_replace( '/\s+/', '', $this->application->getTitle() ) . '\\',
            'Controller'
        );
        
        $useStatements = new UseStatementGenerator([
            \Vankosoft\ApplicationBundle\Controller\AbstractCrudController::class,
            \Symfony\Component\HttpFoundation\Request::class,
        ]);
        
        $generator->generateController(
            $controllerClassDetails->getFullName(),
            $this->makerTemplatesPath . '/resource/controller/Controller.tpl.php',
            [
                'use_statements'    => $useStatements,
                'form_name'         => strtolower( $entityClassDetails->getShortName() ) . '_form'
            ]
        );
        $generator->writeChanges();
        
        return $controllerClassDetails;
    }
    
    protected function generateAssets( InputInterface $input, ConsoleStyle $io, Generator $generator, ClassNameDetails $entityClassDetails )
    {
        
    }
    
    protected function generateTemplates( InputInterface $input, ConsoleStyle $io, Generator $generator, ClassNameDetails $entityClassDetails, EntityDetails $entityDoctrineDetails )
    {
        $io->success( "Now generating Templates." );
        
        $entityVarPlural        = lcfirst( $this->inflector->pluralize( $entityClassDetails->getShortName() ) );
        $entityVarSingular      = lcfirst( $this->inflector->singularize( $entityClassDetails->getShortName() ) );
        
        $entityTwigVarPlural    = Str::asTwigVariable( $entityVarPlural );
        $entityTwigVarSingular  = Str::asTwigVariable( $entityVarSingular );
        
        $relativeTemplatesPath  = \str_replace( $generator->getRootDirectory() . "/templates/", "", $this->templatesPath );
        
        $templates      = [
            '_form' => [],
            'index' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'route_name' => $this->resourceRoute,
                'templates_path' => $relativeTemplatesPath,
            ],
            'create' => [
                'templates_path' => $relativeTemplatesPath,
            ],
            'update' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $this->resourceRoute,
                'templates_path' => $relativeTemplatesPath,
            ],
            'show' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'route_name' => $this->resourceRoute,
                'templates_path' => $relativeTemplatesPath,
            ],
        ];
        
        $makerTemplatesPath = $this->makerTemplatesPath . '/resource/templates/' . ( $this->applicationHasTheme ? 'theme/' : 'default/' );
        foreach ( $templates as $template => $variables ) {
            $generator->generateFile(
                $this->templatesPath . '/'. $template . '.html.twig',
                $makerTemplatesPath . $template . '.tpl.php',
                $variables
            );
        }
        $generator->writeChanges();
    }
}
