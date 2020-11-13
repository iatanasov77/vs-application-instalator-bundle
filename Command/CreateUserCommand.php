<?php namespace VS\UsersBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use VS\UsersBundle\Model\UserInterface;
use VS\UsersBundle\Component\Manager\UserManager;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'vs:user:create';
    
    private $userManager;
    
    public function __construct( UserManager $userManager )
    {
        $this->userManager  = $userManager;
        
        parent::__construct();
    }
    
    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription( 'Creates a new user.' )
            
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp( 'This command allows you to create a user...' )
            
            ->addArgument( 'username', InputArgument::REQUIRED, 'The username of the user.' )
            ->addArgument( 'password', InputArgument::REQUIRED, 'User password' )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $output->writeln([
            'User Creator',
            '==============',
            '',
        ]);
        
        $user = $this->configureNewUser( $input, $output );

        $this->userManager->saveUser( $user );
        
        $output->writeln([
            '',
            '-------------------------------',
            'User successfully generated!',
            '',
        ]);
        
        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;
        
        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;
    }
    
    private function configureNewUser( InputInterface $input, OutputInterface $output ): UserInterface
    {
        $username   = $input->getArgument( 'username' );
        $password   = $input->getArgument( 'password' );
        
        $output->writeln( 'Username: ' . $username );
        $output->writeln( 'Password: ' . $password );
        
        $user   = $this->userManager->createUser( $username, $password );
        
        $user->setEnabled( true );
        //$user->setLocaleCode( $localeCode );
        
        return $user;
    }
}
