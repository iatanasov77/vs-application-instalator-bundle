<?= "<?php" ?> namespace <?= $namespace ?>;<?= "\n" ?>

<?= $use_statements; ?>

class <?= $class_name ?> extends AbstractForm
{
	protected $requestStack;
    
    public function __construct(
        string $dataClass,
        RequestStack $requestStack
    ) {
        parent::__construct( $dataClass );
        
        $this->requestStack	= $requestStack;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    	parent::buildForm( $builder, $options );
        
        $entity         = $builder->getData();
        $currentLocale  = $this->requestStack->getCurrentRequest()->getLocale();
        
        $builder
        	->add( 'locale', ChoiceType::class, [
                'label'                 => 'vs_cms.form.locale',
                'translation_domain'    => 'VSCmsBundle',
                'choices'               => \array_flip( $this->fillLocaleChoices() ),
                'data'                  => $currentLocale,
                'mapped'                => false,
            ])
            
<?php foreach ($form_fields as $form_field => $typeOptions): ?>
<?php if (null === $typeOptions['type'] && !$typeOptions['options_code']): ?>
            ->add('<?= $form_field ?>')
<?php elseif (null !== $typeOptions['type'] && !$typeOptions['options_code']): ?>
            ->add('<?= $form_field ?>', <?= $typeOptions['type'] ?>::class)
<?php else: ?>
            ->add('<?= $form_field ?>', <?= $typeOptions['type'] ? ($typeOptions['type'].'::class') : 'null' ?>, [
<?= $typeOptions['options_code']."\n" ?>
            ])
<?php endif; ?>
<?php endforeach; ?>
        ;
    }

    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
    }
    
    public function getName(): string
    {
        return '<?= $form_name ?>';
    }
}
