<?php namespace IA\CmsBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use IA\CmsBundle\Form\Type\TaxonomyTermType;

class TaxonomyVocabularyForm extends AbstractResourceType implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    public function __construct( $container = null )
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'ia_cms_taxonomy_vocabularies';
    }

    public function buildForm( FormBuilderInterface $builder, array $options )
    {        
        $builder
            ->add( 'enabled', CheckboxType::class, ['label' => 'Enabled'] )
            ->add( 'name', TextType::class, ['label' => 'Title'] )
            ->add( 'description', TextType::class, ['label' => 'Description'] )
            
//            ->add('terms', 'collection', array(
//                'type'         => new TaxonomyTermType(),
//                'allow_add'    => true,
//                'allow_delete' => true,
//                'prototype'    => true,
//                'by_reference' => false
//            ))
                
            ->add( 'btnSave', SubmitType::class, ['label' => 'Save'] )
            ->add( 'btnCancel', ButtonType::class, ['label' => 'Cancel'] )
        ;
    }

    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver->setDefaults([
            'data_class' => 'IA\CmsBundle\Entity\TaxonomyVocabulary'
        ]);
    }

}

