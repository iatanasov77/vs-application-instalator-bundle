<?php namespace Vankosoft\ApplicationBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\ApplicationBundle\Component\Widget\WidgetInterface;

#[AsCommand(
    name: 'vankosoft:load-widgets',
    description: 'Load widgets for all users.',
    hidden: false
)]
class LoadWidgetsCommand extends Command
{
    /** @var RepositoryInterface */
    protected $usersRepository;
    
    /** @var WidgetInterface */
    protected $widgets;
    
    public function __construct( RepositoryInterface $usersRepository, WidgetInterface $widgets )
    {
        parent::__construct();
        
        $this->usersRepository  = $usersRepository;
        $this->widgets          = $widgets;
    }
    
    protected function configure(): void
    {
        $this
        ->setHelp(<<<EOT
Usage: <info>bin/console vankosoft:load-widgets</info>
EOT
            )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $style  = new SymfonyStyle( $input, $output );
        
        $users  = $this->usersRepository->findAll();
        foreach ( $users as $user ) {
            $this->widgets->loadWidgets( $user, false, true );
        }
        
        $style->success( 'Successfully load widgets !!!' );
        $style->newLine();
        
        return Command::SUCCESS;
    }
}