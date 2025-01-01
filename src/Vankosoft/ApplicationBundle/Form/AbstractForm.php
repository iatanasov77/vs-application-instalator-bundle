<?php namespace Vankosoft\ApplicationBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

use Vankosoft\ApplicationBundle\Component\Exception\FormInitializationException;

class AbstractForm extends AbstractResourceType
{
    /** @var RepositoryInterface */
    protected $localesRepository;
    
    /** @var RequestStack */
    protected $requestStack;
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'btnApply', SubmitType::class, ['label' => 'vs_application.form.apply', 'translation_domain' => 'VSApplicationBundle',] )
            ->add( 'btnSave', SubmitType::class, ['label' => 'vs_application.form.save', 'translation_domain' => 'VSApplicationBundle',] )
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
    }
    
    /**
     * Get Locales ChoiceType choices
     * 
     * @throws FormInitializationException
     * @return array
     */
    protected function fillLocaleChoices(): array
    {
        if ( ! $this->localesRepository && ! $this->requestStack ) {
            throw new FormInitializationException( 'To Can Fill Locale Choices Needs Locales Repository and Request Stack.' );
        }
        
        $results = $this->localesRepository->findAll();
        
        $locales = [];
        foreach( $results as $le ){
            $locales[$le->getCode()] = $le->getTitle();
        }
        
        return $locales;
    }
}

