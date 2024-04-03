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

class ProjectIssueForm extends AbstractType
{
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
        
        $resolver
            ->setDefaults([
                'csrf_protection'   => false,
                
                // CKEditor Options
                'ckeditor_uiColor'              => '#ffffff',
                'ckeditor_toolbar'              => 'full',
                'ckeditor_extraPlugins'         => '',
                'ckeditor_removeButtons'        => '',
                'ckeditor_allowedContent'       => false,
                'ckeditor_extraAllowedContent'  => '*[*]{*}(*)',
            ])
            
            ->setDefined([
                // CKEditor Options
                'ckeditor_uiColor',
                'ckeditor_toolbar',
                'ckeditor_extraPlugins',
                'ckeditor_removeButtons',
                'ckeditor_allowedContent',
                'ckeditor_extraAllowedContent',
            ])
            
            ->setAllowedTypes( 'ckeditor_uiColor', 'string' )
            ->setAllowedTypes( 'ckeditor_toolbar', 'string' )
            ->setAllowedTypes( 'ckeditor_extraPlugins', 'string' )
            ->setAllowedTypes( 'ckeditor_removeButtons', 'string' )
            ->setAllowedTypes( 'ckeditor_allowedContent', ['boolean', 'string'] )
            ->setAllowedTypes( 'ckeditor_extraAllowedContent', 'string' )
        ;
    }
    
    public function getName()
    {
        return 'vs_application_project_issue';
    }
    
    protected function ckEditorConfig( array $options ): array
    {
        $ckEditorConfig = [
            'uiColor'                           => $options['ckeditor_uiColor'],
            'toolbar'                           => $options['ckeditor_toolbar'],
            'extraPlugins'                      => array_map( 'trim', explode( ',', $options['ckeditor_extraPlugins'] ) ),
            'removeButtons'                     => $options['ckeditor_removeButtons'],
        ];
        
        $ckEditorAllowedContent = (bool)$options['ckeditor_allowedContent'];
        if ( $ckEditorAllowedContent ) {
            $ckEditorConfig['allowedContent']       = $ckEditorAllowedContent;
        } else {
            $ckEditorConfig['extraAllowedContent']  = $options['ckeditor_extraAllowedContent'];
        }
        
        return $ckEditorConfig;
    }
}
