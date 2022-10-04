<?php namespace Vankosoft\ApplicationBundle\Maker\Renderer;

use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Vankosoft\ApplicationBundle\Form\AbstractForm;

final class FormTypeRenderer
{
    private string $makerTemplatesPath;
    private Generator $generator;
    
    public function __construct(
        KernelInterface $kernel,
        Generator $generator
    ) {
        $this->makerTemplatesPath   = $kernel->locateResource( '@VSApplicationBundle/Resources/maker' );
        $this->generator            = $generator;
    }
    
    public function render(
        ClassNameDetails $formClassDetails,
        array $formFields,
        ClassNameDetails $boundClassDetails = null,
        array $constraintClasses = [],
        array $extraUseClasses = [],
        string $formName = ''
    ): void {
        $fieldTypeUseStatements = [];
        $fields = [];
        foreach ($formFields as $name => $fieldTypeOptions) {
            $fieldTypeOptions ??= ['type' => null, 'options_code' => null];
            
            if (isset($fieldTypeOptions['type'])) {
                $fieldTypeUseStatements[] = $fieldTypeOptions['type'];
                $fieldTypeOptions['type'] = Str::getShortClassName($fieldTypeOptions['type']);
            }
            
            $fields[$name] = $fieldTypeOptions;
        }
        
        $useStatements = new UseStatementGenerator(array_unique(array_merge(
            $fieldTypeUseStatements,
            $extraUseClasses,
            $constraintClasses
        )));
        
        $useStatements->addUseStatement([
            //AbstractType::class,
            AbstractForm::class,
            FormBuilderInterface::class,
            OptionsResolver::class,
            RequestStack::class,
            
            \Symfony\Component\Form\Extension\Core\Type\HiddenType::class,
            \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class,
            \Symfony\Component\Form\Extension\Core\Type\TextType::class,
        ]);
        
        if ($boundClassDetails) {
            $useStatements->addUseStatement($boundClassDetails->getFullName());
        }
        
        $this->generator->generateClass(
            $formClassDetails->getFullName(),
            $this->makerTemplatesPath . '/resource/form/Type.tpl.php',
            [
                'use_statements'        => $useStatements,
                'bounded_class_name'    => $boundClassDetails ? $boundClassDetails->getShortName() : null,
                'form_fields'           => $fields,
                'form_name'             => $formName,
            ]
        );
    }
}
