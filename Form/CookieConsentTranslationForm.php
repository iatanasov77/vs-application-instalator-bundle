<?php namespace Vankosoft\ApplicationBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CookieConsentTranslationForm extends AbstractForm
{
    public function __construct(
        string $dataClass,
        RepositoryInterface $localesRepository
    ) {
        parent::__construct( $dataClass );
        
        $this->localesRepository    = $localesRepository;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $entity             = $builder->getData();
        $locales            = $this->localesRepository->findAll();
        
        $availableLocales   = [];
        $languageCodes      = [];
        foreach( $locales as $locale ) {
            $lang                       = \explode( '_', $locale->getCode() );
            
            $availableLocales[$lang[0]] = $locale->getCode();
            $languageCodes[$lang[0]]    = $locale->getTitle();
        }
        
        $builder
            ->add( 'availableLocales', HiddenType::class, [
                'mapped'    => false,
                'data'      => \json_encode( $availableLocales )
            ])
            
            ->add( 'localeCode', HiddenType::class )
        
            ->add( 'languageCode', ChoiceType::class, [
                'label'                 => 'vs_application.form.cookie_consent_translation.language_code',
                'translation_domain'    => 'VSApplicationBundle',
                'choices'               => \array_flip( $languageCodes ),
                'placeholder'           => 'vs_application.form.cookie_consent_translation.language_code_placeholder',
            ])
            
            ->add( 'btnAcceptAll', TextType::class, [
                'label'                 => 'vs_application.form.cookie_consent_translation.button_accept_all',
                'translation_domain'    => 'VSApplicationBundle',
            ])
            
            ->add( 'btnRejectAll', TextType::class, [
                'label'                 => 'vs_application.form.cookie_consent_translation.button_reject_all',
                'translation_domain'    => 'VSApplicationBundle',
            ])
            
            ->add( 'btnAcceptNecessary', TextType::class, [
                'label'                 => 'vs_application.form.cookie_consent_translation.button_accept_necessary',
                'translation_domain'    => 'VSApplicationBundle',
            ])
            
            ->add( 'btnShowPreferences', TextType::class, [
                'label'                 => 'vs_application.form.cookie_consent_translation.button_show_preferences',
                'translation_domain'    => 'VSApplicationBundle',
            ])
            
            ->add( 'label', TextType::class, [
                'label'                 => 'vs_application.form.label',
                'translation_domain'    => 'VSApplicationBundle',
            ])
            
            ->add( 'title', TextType::class, [
                'label'                 => 'vs_application.form.title',
                'translation_domain'    => 'VSApplicationBundle',
            ])
            
            ->add( 'description', TextareaType::class, [
                'label'                 => 'vs_application.form.description',
                'translation_domain'    => 'VSApplicationBundle'
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
        return 'vs_application.cookie_consent_translation';
    }
}
