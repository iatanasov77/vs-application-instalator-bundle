<?php namespace Vankosoft\ApplicationBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class TagsWhitelistContextForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'name', TextType::class, ['label' => 'vs_application.form.title', 'translation_domain' => 'VSApplicationBundle',] )
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
        return 'vs_application.tags_whitelist_context';
    }
}