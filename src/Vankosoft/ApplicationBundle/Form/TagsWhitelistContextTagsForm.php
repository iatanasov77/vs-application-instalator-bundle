<?php namespace Vankosoft\ApplicationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Vankosoft\ApplicationBundle\Form\Type\WhitelistContextTagType;
use Vankosoft\ApplicationBundle\Model\Interfaces\TagsWhitelistContextInterface;

class TagsWhitelistContextTagsForm extends AbstractType
{
    /** @var string */
    private $dataClass;
    
    public function __construct(
        string $dataClass
    ) {
        $this->dataClass    = $dataClass;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'tags', CollectionType::class, [
                'entry_type'   => WhitelistContextTagType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'by_reference' => false
            ])
            
            ->add( 'btnSubmitTags', SubmitType::class, [
                'label'                 => 'vs_application.form.save_tags',
                'translation_domain'    => 'VSApplicationBundle',
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefaults([
            'csrf_protection'   => false,
            'data_class'        => $this->dataClass
        ]);
    }
    
    public function getName()
    {
        return 'vs_application.tags_whitelist_context_tags';
    }
}