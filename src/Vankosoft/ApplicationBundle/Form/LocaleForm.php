<?php namespace Vankosoft\ApplicationBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Vankosoft\ApplicationBundle\Component\I18N;

class LocaleForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'translatableLocale', ChoiceType::class, [
                'label'                 => 'vs_application.form.translatable_locale',
                'translation_domain'    => 'VSApplicationBundle',
                'choices'               => \array_flip( I18N::LanguagesAvailable() ),
                'data'                  => \Locale::getDefault(),
                'mapped'                => false,
            ])
        
            ->add( 'code', TextType::class, [
                'label' => 'vs_application.form.locale_code',
                'translation_domain' => 'VSApplicationBundle',
            ])
            
            ->add( 'title', TextType::class, [
                'label' => 'vs_application.form.title',
                'translation_domain' => 'VSApplicationBundle',
            ])
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
        return 'vs_application.locale';
    }
}
