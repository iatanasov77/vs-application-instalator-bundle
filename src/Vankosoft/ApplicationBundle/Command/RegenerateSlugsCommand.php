<?php namespace Vankosoft\ApplicationBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Gedmo\Sluggable\Util as Sluggable;

class RegenerateSlugsCommand extends Command
{
    private $doctrine;
    
    /**
     * Using: bin/console vankosoft:regenerate-slugs \App\Entity\Foo
     * 
     * @var string
     */
    protected static $defaultName = "vankosoft:regenerate-slugs";
    
    public function __construct( ManagerRegistry $doctrine )
    {
        parent::__construct();
        
        $this->doctrine = $doctrine;
    }
    
    protected function configure(): void
    {
        $this
            ->setDescription( 'Regenerate the slugs for all Foo and Bar entities.' )
            ->addArgument( 'classWithNamespace', InputArgument::REQUIRED, 'The Full Class Path?' )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $fullClassPath  = $input->getArgument( 'classWithNamespace' );
        
        $manager = $this->doctrine->getManager();

        // Change the next line by your classes
        //foreach ( [\App\Entity\Foo::class, \App\Entity\Bar::class] as $class ) {
        foreach ( [$fullClassPath] as $class ) {
            foreach ( $manager->getRepository( $class )->findAll() as $entity ) {
                $slug   = Sluggable\Urlizer::urlize( $entity->getTitle(), '-' );
                $entity->setSlug( $slug );
            }
            
            $manager->flush();
            $manager->clear();
            
            $output->writeln( "Slugs of \"$class\" updated." );
        }
        
        return Command::SUCCESS;
    }
}
