<?php namespace Vankosoft\ApplicationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class WhitelistContextTagType extends AbstractType
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
            ->add( 'id', HiddenType::class, ['mapped' => false] )
            ->add( 'tag', TextType::class, [
                'required'              => false,
                'translation_domain'    => 'VSApplicationBundle',
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'data_class'        => $this->dataClass
        ));
    }
    
    public function getName()
    {
        return 'FormFieldsetField';
    }
}
