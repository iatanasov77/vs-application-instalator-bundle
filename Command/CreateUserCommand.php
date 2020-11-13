<?php namespace VS\UsersBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use VS\UsersBundle\Model\UserInterface;

class CreateUserCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'vs:user:create';
    
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
        
        $userFactory = $this->getContainer()->get( 'vs_users.factory.users' );
        $user = $this->configureNewUser( $userFactory->createNew(), $input, $output );
       
        $user->setEnabled( true );
        //$user->setLocaleCode( $localeCode );
        
        $em = $this->getContainer()->get( 'doctrine.orm.entity_manager' );
        $em->persist( $user );
        $em->flush();
        
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
    
    private function configureNewUser(
        UserInterface $user,
        InputInterface $input,
        OutputInterface $output
    ): UserInterface {
        $encoder    = $this->getContainer()->get( 'security.encoder_factory' )->getEncoder( $user );
        
        $username   = $input->getArgument( 'username' );
        $password   = $input->getArgument( 'password' );
        $salt       = md5( time() );
        
        $output->writeln( 'Username: ' . $username );
        $output->writeln( 'Password: ' . $password );
        
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->getContainer()->get( 'vs_users.repository.users' );
        
        if ($input->getOption('no-interaction')) {
            Assert::null( $userRepository->findOneByEmail( 'sylius@example.com' ) );
            
            $user->setEmail( 'admin@example.com' );
            $user->setUsername( 'admin' );
            $user->setPassword( $encoder->encodePassword( 'admin', $salt ) );
            
            return $user;
        }
        
        $user->setEmail( $username );
        $user->setUsername( $username );
        $user->setPassword( $encoder->encodePassword( $password, $salt ) );
        
        return $user;
    }
}
