<?php namespace Vankosoft\ApplicationBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\FixedCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Vankosoft\ApplicationBundle\Repository\Interfaces\ApplicationRepositoryInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;

final class ApplicationCollectionType extends AbstractType
{
    /** @var ApplicationRepositoryInterface */
    private $applicationRepository;
    
    public function __construct( ApplicationRepositoryInterface $applicationRepository )
    {
        $this->applicationRepository    = $applicationRepository;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver->setDefaults([
            'entries'           => $this->applicationRepository->findAll(),
            'entry_name'        => fn ( ApplicationInterface $application ) => $application->getCode(),
            'error_bubbling'    => false,
        ]);
    }
    
    public function buildView( FormView $view, FormInterface $form, array $options ): void
    {
        $children = $form->all();
        
        $view->vars['applications_errors_count'] = array_combine(
            array_keys( $children ),
            array_map(
                static fn ( FormInterface $child ): int => $child->getErrors( true )->count(),
                $children,
            ),
        );
    }
    
    public function getParent(): string
    {
        return FixedCollectionType::class;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_application_application_collection';
    }
}