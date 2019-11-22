<?php

namespace IA\CmsBundle\Form;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;

class PageForm extends AbstractResourceType implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    public function __construct($container = null)
    {
        $this->container = $container;
    }
    
    public function getName()
    {
        return 'ia_cms_pages';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->add( 'enabled', CheckboxType::class, ['label' => 'Enabled'] )
            ->add( 'title', TextType::class, ['label' => 'Title'] )
            ->add( 'slug', TextType::class, ['label' => 'Slug'] )
            
            ->add( 'text', CKEditorType::class, [
                'label'     => 'Page Content',
                'config'    => ['uiColor' => '#ffffff'],
            ])
            
            ->add( 'btnSave', SubmitType::class, ['label' => 'Save'] )
            ->add( 'btnCancel', ButtonType::class, ['label' => 'Cancel'] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => 'IA\CmsBundle\Entity\Page'
        ));
    }

}

