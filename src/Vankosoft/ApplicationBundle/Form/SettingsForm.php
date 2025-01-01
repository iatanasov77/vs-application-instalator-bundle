<?php namespace Vankosoft\ApplicationBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Sylius\Bundle\ThemeBundle\Form\Type\ThemeNameChoiceType;

use Vankosoft\ApplicationBundle\Model\GeneralSettings;

class SettingsForm extends AbstractForm
{
    protected $pageClass;
    
    public function __construct( string $dataClass, string $pageClass )
    {
        parent::__construct( $dataClass );
        
        $this->pageClass = $pageClass;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'maintenanceMode', CheckboxType::class, ['label' => 'vs_application.form.maintenance_mode', 'translation_domain' => 'VSApplicationBundle',] )
            
            ->add( 'maintenancePage', EntityType::class, [
                'label'                 => 'vs_application.form.maintenance_page',
                'translation_domain'    => 'VSApplicationBundle',
                'class'                 => $this->pageClass,
                'placeholder'           => 'vs_application.form.maintenance_page_placeholder',
                'choice_label'          => 'title',
                'required'              => false
            ])
            
            ->add('theme', ThemeNameChoiceType::class, [
                'label'                 => 'vs_application.form.theme',
                'translation_domain'    => 'VSApplicationBundle',
                'required'              => false,
                'empty_data'            => null,
                'placeholder'           => 'vs_application.form.theme_placeholder',
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
        return 'vs_application.general_settings';
    }
}
