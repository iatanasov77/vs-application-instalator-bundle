<?php namespace Vankosoft\ApplicationBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ApplicationForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'enabled', CheckboxType::class, ['label' => 'vs_application.form.enabled', 'translation_domain' => 'VSApplicationBundle'] )
            ->add( 'title', TextType::class, ['label' => 'vs_application.form.title', 'translation_domain' => 'VSApplicationBundle'] )
            ->add( 'hostname', TextType::class, ['label' => 'vs_application.form.hostname', 'translation_domain' => 'VSApplicationBundle'] )
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
    
    public function getName()
    {
        return 'vs_application.application';
    }
}
