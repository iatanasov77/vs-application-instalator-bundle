<?php namespace Vankosoft\ApplicationBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TaxonAutocompleteChoiceType extends AbstractType
{
    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver->setDefaults([
            'resource'      => 'vs_application.taxon',
            'choice_name'   => 'fullname',
            'choice_value'  => 'code',
        ]);
    }
    
    public function buildView( FormView $view, FormInterface $form, array $options ): void
    {
        $view->vars['remote_criteria_type'] = 'contains';
        $view->vars['remote_criteria_name'] = 'phrase';
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_application_taxon_autocomplete_choice';
    }
    
    public function getParent(): string
    {
        return ResourceAutocompleteChoiceType::class;
    }
}
