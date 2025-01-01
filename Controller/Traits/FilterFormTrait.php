<?php namespace Vankosoft\ApplicationBundle\Controller\Traits;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

trait FilterFormTrait
{
    protected function getFilterForm( $filterClass, $selected, $request )
    {
        $filterForm     = $this->createFormBuilder()
                            ->add( 'filterByCategory', EntityType::class, [
                                'class'                 => $filterClass,
                                'choice_label'          => function ( $category ) use ( $request )
                                {
                                    return $category->getNameTranslated( $request->getLocale() );
                                },
                                'required'              => true,
                                'label'                 => 'vs_application.form.filter_by_category',
                                'placeholder'           => 'vs_application.form.category_placeholder',
                                'translation_domain'    => 'VSApplicationBundle',
                                'data'                  => $selected ?
                                                            $this->getFilterRepository()->find( $selected ) :
                                                            null,
                            ])
                            ->getForm();
        
        return $filterForm;
    }
    
    abstract protected function getFilterRepository();
}
