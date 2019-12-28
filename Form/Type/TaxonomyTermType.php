<?php namespace IA\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaxonomyTermType extends AbstractType
{

    public function getName()
    {
        return 'taxonomy_terms';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add( 'name', TextType::class )
            ->add( 'description', TextareaType::class, ['required'=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'IA\CmsBundle\Entity\TaxonomyTerm'
        ]);
    }

}

