<?php namespace IA\CmsBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

use IA\CmsBundle\Entity\Repository\TaxonomyTermsRepository;

class TaxonomyTermForm extends AbstractResourceType
{

    public function getName()
    {
        return 'ia_cms_taxonomy_terms';
    }

    public function buildForm( FormBuilderInterface $builder, array $options )
    {   
        $vocabulary = $options['data']->getVocabulary();
        $builder
            ->add( 'enabled', CheckboxType::class, ['label' => 'Enabled'] )
            ->add( 'name', TextType::class, ['label' => 'Title'] )
                 
            ->add( 'parent', HiddenType::class )
//            ->add('parent', 'entity', array(
//                'required' => false,
//                'label' => 'Parent',
//                'class' => 'IATaxonomyBundle:Term',
//                'attr' => array('class' => 'col-sm-8'),
//                'empty_value' => 'Select a term',
//                'property' => 'name',
//                'multiple' => false,
//                'expanded' => false ,
//                'query_builder' => function (TermsRepository $r)
//                {
//                    $queryBuilder = $r->createQueryBuilder('t');
//                    $query = $queryBuilder
//                        ->where($queryBuilder->expr()->isNotNull('t.parent'))
//                    ;
//
//                    return $query;
//                }
//            ))
            
            ->add( 'description', TextareaType::class, ['label' => 'Description', 'required' => false] )
                
            ->add( 'btnSave', SubmitType::class, ['label' => 'Save'] )
            ->add( 'btnCancel', ButtonType::class, ['label' => 'Cancel'] )
        ;
    }

    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver->setDefaults([
            'data_class' => 'IA\CmsBundle\Entity\TaxonomyTerm'
        ]);
    }

}

