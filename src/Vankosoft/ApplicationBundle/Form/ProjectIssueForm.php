<?php namespace Vankosoft\ApplicationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Vankosoft\ApplicationBundle\Component\Application\ProjectIssue;
use Vankosoft\CmsBundle\Form\Traits\FosCKEditor4Config;

class ProjectIssueForm extends AbstractType
{
    use FosCKEditor4Config;
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
       $builder
            ->add( 'title', TextType::class, [
                'label'                 => 'vs_application.form.project_issue.title',
                'translation_domain'    => 'VSApplicationBundle',
                'required'              => true
            ])
            
            ->add( 'description', CKEditorType::class, [
                'label'                 => 'vs_application.form.project_issue.description',
                'translation_domain'    => 'VSApplicationBundle',
                'required'              => false,
                'config'                => $this->ckEditorConfig( $options ),
            ])
            
            ->add( 'status', ChoiceType::class, [
                'label'                 => 'vs_application.form.project_issue.status',
                'translation_domain'    => 'VSApplicationBundle',
                'choices'               => \array_flip( ProjectIssue::ISSUE_STATUS ),
                'expanded'              => true,
                'required'              => false,
                'placeholder'           => false,
            ])
            
            ->add( 'labelsWhitelist', HiddenType::class, ['mapped' => false] )
            ->add( 'labels', TextType::class, [
                'label'                 => 'vs_application.form.project_issue.labels',
                'translation_domain'    => 'VSApplicationBundle',
                'required'              => false,
            ])
            
            ->add( 'btnApply', SubmitType::class, ['label' => 'vs_application.form.apply', 'translation_domain' => 'VSApplicationBundle',] )
            ->add( 'btnSave', SubmitType::class, ['label' => 'vs_application.form.save', 'translation_domain' => 'VSApplicationBundle',] )
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefaults( ['csrf_protection'   => false] );
        $this->onfigureCkEditorOptions( $resolver );
    }
    
    public function getName()
    {
        return 'vs_application_project_issue';
    }
}
