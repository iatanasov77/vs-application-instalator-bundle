<?php namespace VS\UsersBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use VS\UsersBundle\Model\UserInterface;

/*
 * Form Inheritance:
 * https://stackoverflow.com/questions/22414166/inherit-form-or-add-type-to-each-form
 */
class RegistrationFormType extends UserFormType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        parent::buildForm( $builder, $options );
        
        $builder->remove( 'roles_options' );
        $builder
            ->setMethod( 'POST' )
            ->add( 'agreeTerms', CheckboxType::class, [
                'label'                 => 'vs_users.registration.agree_terms',
                'translation_domain'    => 'VSUsersBundle',
                "mapped"                => false,
            ])
//             ->add( 'profile', new ProfileFormType(), array(
//                 'label' => false,
//             ))
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ) : void
    {   
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefined([
                'users',
            ])
            ->setAllowedTypes( 'users', UserInterface::class )
        ;
    }

    public function getName()
    {
        return 'vs_users.registration';
    }
}
