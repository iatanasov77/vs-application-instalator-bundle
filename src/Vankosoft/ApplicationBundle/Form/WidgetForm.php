<?php namespace Vankosoft\ApplicationBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Vankosoft\UsersBundle\Component\UserRole;

class WidgetForm extends AbstractForm
{
    /** @var string */
    private $groupClass;
    
    /** @var string */
    private $userRolesClass;
    
    public function __construct(
        string $dataClass,
        RequestStack $requestStack,
        RepositoryInterface $localesRepository,
        string $groupClass,
        string $userRolesClass
    ) {
        parent::__construct( $dataClass );
        
        $this->requestStack         = $requestStack;
        $this->localesRepository    = $localesRepository;
        
        $this->groupClass           = $groupClass;
        $this->userRolesClass       = $userRolesClass;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $entity         = $builder->getData();
        $currentLocale  = $entity->getTranslatableLocale() ?: $this->requestStack->getCurrentRequest()->getLocale();
        
        $builder
            ->add( 'locale', ChoiceType::class, [
                'label'                 => 'vs_application.form.locale',
                'translation_domain'    => 'VSApplicationBundle',
                'choices'               => \array_flip( $this->fillLocaleChoices() ),
                'data'                  => $currentLocale,
                'mapped'                => false,
            ])
            
            ->add( 'enabled', CheckboxType::class, [
                'label'                 => 'vs_application.form.active',
                'translation_domain'    => 'VSApplicationBundle',
            ])
            
            ->add( 'group', EntityType::class, [
                'label'                 => 'vs_application.form.group_label',
                'required'              => true,
                'class'                 => $this->groupClass,
                'choice_label'          => 'name',
                'placeholder'           => 'vs_application.form.group_placeholder',
                'translation_domain'    => 'VSApplicationBundle',
            ])
        
            ->add( 'name', TextType::class, [
                'label'                 => 'vs_application.form.title',
                'translation_domain'    => 'VSApplicationBundle',
            ])
            
            ->add( 'description', TextareaType::class, [
                'label'                 => 'vs_application.form.description',
                'translation_domain'    => 'VSApplicationBundle',
                'required'              => false,
            ])
            
            ->add( 'allowedRoles', EntityType::class, [
                'label'                 => 'vs_application.form.allowed_roles_label',
                'placeholder'           => 'vs_application.form.allowed_roles_placeholder',
                'translation_domain'    => 'VSApplicationBundle',
                'mapped'                => false,
                'multiple'              => true,
                'class'                 => $this->userRolesClass,
                'choice_label'          => 'role',
                
                // Combotree Makes Error on Chrome if field is required 
                'required'              => false,
            ])
            
            ->add( 'allowAnonymous', CheckboxType::class, [
                'label'                 => 'vs_application.form.allow_anonymous',
                'translation_domain'    => 'VSApplicationBundle',
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
    
    public function getName()
    {
        return 'vs_application.widgets_group';
    }
}