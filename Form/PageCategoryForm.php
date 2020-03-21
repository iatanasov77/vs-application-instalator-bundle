<?php namespace IA\CmsBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\PostSetDataEvent;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

use IA\CmsBundle\Entity\PageCategory;

class PageCategoryForm extends AbstractResourceType implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    public function __construct( $container = null )
    {
        $this->container = $container;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'name', TextType::class, ['label' => 'Title'] )
            
            ->add( 'parent', EntityType::class, [
                'label'         => 'Parent Category',
                'class'         => PageCategory::class,
                'placeholder'   => '-- Set As Root --',
                'choice_label'  => 'name',
                'required'      => false
            ])

            ->add( 'btnSave', SubmitType::class, ['label' => 'Save'] )
            ->add( 'btnCancel', ButtonType::class, ['label' => 'Cancel'] )
        ;
        
        /*
        $builder->addEventListener( FormEvents::POST_SET_DATA, function ( PostSetDataEvent $event )
        {
            $parent   = $event->getData()->getParent();
            if( $parent) {
                $event->getForm()->get( "parentId" )->setData( $parent->getId() );
            }
        });
        */
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver->setDefaults([
            'data_class' => PageCategory::class
        ]);
    }
}
