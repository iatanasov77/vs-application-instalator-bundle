<?php

namespace IA\CmsBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class PageForm extends AbstractResourceType
{

    public function getName()
    {
        return 'ia_cms_pages';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->add('enabled', 'checkbox', array('label' => 'Enabled'))
            ->add('title', 'text', array('label' => 'Title'))
            ->add('slug', 'text', array('label' => 'Slug'))
                
            ->add('text', CKEditorType::class, array(
                'label' => 'Page Content',
                'config' => array(
                    'uiColor' => '#ffffff',
                    //...
                ),
            ))
           
            ->add('btnSave', 'submit', array('label' => 'Save'))
            ->add('btnCancel', 'button', array('label' => 'Cancel'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => 'IA\CmsBundle\Entity\Page'
        ));
    }

}

