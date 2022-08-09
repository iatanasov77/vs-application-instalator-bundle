<?php namespace Vankosoft\ApplicationBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Gedmo\Sluggable\Util as Sluggable;  

class RegenerateSlugsCommand extends Command
{
    private $doctrine;
    
    protected static $defaultName = "vankosoft:regenerate-slugs";
    
    public function __construct( ManagerRegistry $doctrine )
    {
        parent::__construct();
        
        $this->doctrine = $doctrine;
    }
    
    protected function configure(): void
    {
        $this
            ->setDescription('Regenerate the slugs for all Foo and Bar entities.')
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $manager = $this->doctrine->getManager();

        // Change the next line by your classes
        //foreach ( [\App\Entity\Foo::class, \App\Entity\Bar::class] as $class ) {
        foreach ( [\App\Entity\Project::class] as $class ) {
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
