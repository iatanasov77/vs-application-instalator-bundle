<?php  namespace Vankosoft\ApplicationBundle\Maker;

use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Vankosoft\ApplicationBundle\Component\SlugGenerator;

final class MakeTheme extends AbstractMaker
{
    protected string $makerTemplatesPath;
    protected SlugGenerator $slugGenerator;
    
    public function __construct( KernelInterface $kernel, SlugGenerator $slugGenerator )
    {
        $this->makerTemplatesPath   = $kernel->locateResource( '@VSApplicationBundle/Resources/maker' );
        $this->slugGenerator        = $slugGenerator;
    }
    
    public static function getCommandName(): string
    {
        return 'vankosoft:make:theme';
    }
    
    public static function getCommandDescription(): string
    {
        return 'Creates or updates a Doctrine entity class, and optionally an API Platform resource';
    }
    
    public function configureCommand( Command $command, InputConfiguration $inputConfig ): void
    {
        $command
            ->addArgument( 'name', InputArgument::OPTIONAL, sprintf( 'Theme name to create', Str::asClassName( Str::getRandomTerm() ) ) )
            ->addOption( 'clone-theme', null, InputOption::VALUE_REQUIRED, 'Which Theme to Clone ', 'ApplicationSimpleTheme' )
            ->setHelp( file_get_contents( __DIR__ . '/../Resources/maker-help/MakeTheme.txt' ) )
        ;
        
        $inputConfig->setArgumentAsNonInteractive('name');
    }
    
    /**
     * Configure any library dependencies that your maker requires.
     * @param DependencyBuilder $dependencies
     */
    public function configureDependencies( DependencyBuilder $dependencies )
    {
        // TODO: Implement configureDependencies() method.
    }
    
    public function interact( InputInterface $input, ConsoleStyle $io, Command $command ): void
    {
        if ( $input->getArgument( 'name' ) ) {
            return;
        }
    }
    
    public function generate( InputInterface $input, ConsoleStyle $io, Generator $generator ): void
    {
        $cloneThemePath = $generator->getRootDirectory() . '/themes/' . $input->getOption( 'clone-theme' );
        $themePath      = $generator->getRootDirectory() . '/themes/' . \preg_replace( '/\s+/', '', $input->getArgument( 'name' ) );
        $filesystem     = new Filesystem();
        
        $io->info( "Theme to creating: " . $input->getArgument( 'name' ) );
        if ( ! $filesystem->exists( $cloneThemePath ) ) {
            $io->error( 'Theme to Clone Not Exists !!!' );
            
            return;
        }
        
        if ( $filesystem->exists( $themePath ) ) {
            $io->error( 'Cannot Create Theme Because Theme Directory Already Exist !!!' );
            
            return;
        }
        
        $io->success( "Now creating Theme." );
        $filesystem->mirror( $cloneThemePath, $themePath );
        
        $themeJson  = \json_decode( \file_get_contents( $themePath . '/composer.json' ), true );
        $themeJson['name']  = 'vankosoft/' . $this->slugGenerator->generate( $input->getArgument( 'name' ) );
        $themeJson['extra']['sylius-theme']['title']    = $input->getArgument( 'name' );
        \file_put_contents( $themePath . '/composer.json', \json_encode( $themeJson, JSON_PRETTY_PRINT ) );
    }
}
