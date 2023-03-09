<?php namespace Vankosoft\ApplicationBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Doctrine\ORM\EntityRepository;

class TaxonForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'currentLocale', HiddenType::class )

            ->add( 'parentTaxon', EntityType::class, [
                'mapped'                => false,
                'required'              => true,
                'label'                 => 'vs_application.form.parent',
                'translation_domain'    => 'VSApplicationBundle',
                'class'                 => $this->dataClass,
                'choice_label'          => 'name',
                'query_builder'         => function ( EntityRepository $er ) use ( $options )
                {
                    //var_dump( $er ); die;
                    return $er->createQueryBuilder( 't' )
                                ->where( 't.root = :rootTaxon' )
                                ->setParameter( 'rootTaxon', $options['rootTaxon'] );
                }
            ])
            
            ->add( 'name', TextType::class, ['label' => 'vs_application.form.name', 'translation_domain' => 'VSApplicationBundle',] )
            ->add( 'description', TextareaType::class, [
                'label'                 => 'vs_application.form.description', 
                'translation_domain'    => 'VSApplicationBundle', 
                'required'              => false
            ])
        ;
    }

    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefaults([
            'csrf_protection' => false,
            'rootTaxon' => null,
        ]);
    }
    
    public function getName()
    {
        return 'vs_application.taxon';
    }
}

