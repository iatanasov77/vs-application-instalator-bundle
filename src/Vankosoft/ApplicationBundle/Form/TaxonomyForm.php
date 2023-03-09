<?php namespace Vankosoft\ApplicationBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Vankosoft\ApplicationBundle\Component\I18N;

class TaxonomyForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'locale', ChoiceType::class, [
                'label'                 => 'vs_application.form.locale',
                'translation_domain'    => 'VSApplicationBundle',
                'choices'               => \array_flip( I18N::LanguagesAvailable() ),
                'data'                  => \Locale::getDefault(),
                'mapped'                => false,
            ])
        
            ->add( 'name', TextType::class, ['label' => 'vs_application.form.title', 'translation_domain' => 'VSApplicationBundle',] )
            ->add( 'description', TextType::class, ['label' => 'vs_application.form.description', 'translation_domain' => 'VSApplicationBundle',] )
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
        return 'vs_application.taxonomy';
    }
}
