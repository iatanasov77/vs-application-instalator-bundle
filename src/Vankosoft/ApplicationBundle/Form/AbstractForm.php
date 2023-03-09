<?php namespace Vankosoft\ApplicationBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

use Vankosoft\ApplicationBundle\Component\I18N;

class AbstractForm extends AbstractResourceType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'btnApply', SubmitType::class, ['label' => 'vs_application.form.apply', 'translation_domain' => 'VSApplicationBundle',] )
            ->add( 'btnSave', SubmitType::class, ['label' => 'vs_application.form.save', 'translation_domain' => 'VSApplicationBundle',] )
            ->add( 'btnCancel', ButtonType::class, ['label' => 'vs_application.form.cancel', 'translation_domain' => 'VSApplicationBundle',] )
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
    }
    
}

