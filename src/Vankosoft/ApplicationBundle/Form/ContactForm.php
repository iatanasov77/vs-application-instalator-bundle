<?php namespace Vankosoft\ApplicationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class ContactForm extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->setMethod( 'POST' )
            
            ->add( 'name', TextType::class, [
                'label'                 => 'vs_application.form.website_contact.name',
                'translation_domain'    => 'VSApplicationBundle',
                'attr'                  => ['placeholder' => 'vs_application.form.website_contact.name_placeHolder'],
            ])
            
            ->add( 'email', EmailType::class, [
                'label'                 => 'vs_application.form.website_contact.email',
                'translation_domain'    => 'VSApplicationBundle',
                'attr'                  => ['placeholder' => 'vs_application.form.website_contact.email_placeHolder'],
            ])
            
            ->add( 'subject', TextType::class, [
                'label'                 => 'vs_application.form.website_contact.subject',
                'translation_domain'    => 'VSApplicationBundle',
                'attr'                  => ['placeholder' => 'vs_application.form.website_contact.subject_placeHolder'],
            ])
            
            ->add( 'message', TextareaType::class, [
                'label'                 => 'vs_application.form.website_contact.message',
                'translation_domain'    => 'VSApplicationBundle',
                'attr'                  => ['placeholder' => 'vs_application.form.website_contact.message_placeHolder'],
            ])
            
            ->add( 'btnSubmit', SubmitType::class, [
                'label'                 => 'vs_application.form.website_contact.submit',
                'translation_domain'    => 'VSApplicationBundle'
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ) : void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'csrf_protection'   => true,
            ])
        ;
        
    }
    
    public function getName()
    {
        return 'vs_application.contact';
    }
}
