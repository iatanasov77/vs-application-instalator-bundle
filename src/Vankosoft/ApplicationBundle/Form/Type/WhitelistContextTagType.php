<?php namespace Vankosoft\ApplicationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vankosoft\ApplicationBundle\Model\Interfaces\TagsWhitelistTagInterface;

class WhitelistContextTagType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'tag', TextType::class, [
                'required'              => false,
                'translation_domain'    => 'VSApplicationBundle',
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'data_class'        => TagsWhitelistTagInterface::class
        ));
    }
    
    public function getName()
    {
        return 'FormFieldsetField';
    }
}
