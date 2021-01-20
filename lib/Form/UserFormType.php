<?php namespace VS\UsersBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

use VS\UsersBundle\Form\Type\UserInfoFormType;
use VS\UsersBundle\Model\UserInterface;

class UserFormType extends AbstractResourceType  implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    public function __construct( $container = null )
    {
        $this->container = $container;
    }
    
    public function getName()
    {
        return 'ia_users_users';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->setMethod('PUT')
            //->add('apiKey', HiddenType::class)
            //->add('enabled', CheckboxType::class, array('label' => 'Enabled'))
  
            ->add('email', TextType::class, array('label' => 'registration.email', 'translation_domain' => 'VSUsersBundle'))
            
            ->add('username', TextType::class, array('label' => 'registration.userName', 'translation_domain' => 'VSUsersBundle'))
            
            
            ->add('password', PasswordType::class, array('label' => 'registration.password', 'translation_domain' => 'VSUsersBundle'))
               
//             ->add('userInfo', UserInfoFormType::class, [
//                 'data_class' => UserInfo::class,
//                 'by_reference' => true
//             ])
            
            ->add('btnSave', SubmitType::class, array('label' => 'Save'))
            ->add('btnCancel', ButtonType::class, array('label' => 'Cancel'))
        ;
    }

    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefined([
                'user',
            ])
            ->setAllowedTypes( 'user', UserInterface::class )
        ;
    }

}

