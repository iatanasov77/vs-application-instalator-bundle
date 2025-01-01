<?= "<?php" ?> namespace <?= $namespace ?>;<?= "\n" ?>

<?= $use_statements; ?>

class <?= $class_name ?> extends AbstractCrudController
{
    use TaxonomyHelperTrait;
    
    protected function customData( Request $request, $entity = NULL ): array
    {
        $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
            $this->getParameter( '<?= $taxonomy_code_parameter ?>' )
        );
        
        $translations   = $this->classInfo['action'] == 'indexAction' ? $this->getTranslations() : [];
        if ( $entity && $entity->getTaxon() ) {
            $entity->getTaxon()->setCurrentLocale( $request->getLocale() );
        }
        
    	return [
    	    'taxonomyId'    => $taxonomy->getId(),
            'translations'  => $translations,
            'items'         => $this->getDoctrine()->getRepository( \<?= $entity_class ?>::class )->findBy( ['parent' => null] ),
    	];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $translatableLocale = $form['locale']->getData();
        $this->get( 'vs_application.slug_generator' )->setLocaleCode( $translatableLocale );
        
        $categoryName       = $form['name']->getData();
        
        if ( $entity->getTaxon() ) {
            $entityTaxon    = $entity->getTaxon();
            
            $entityTaxon->getTranslation( $translatableLocale );
            $entityTaxon->setCurrentLocale( $translatableLocale );
            if ( ! in_array( $translatableLocale, $entityTaxon->getExistingTranslations() ) ) {
                $taxonTranslation   = $this->createTranslation( $entityTaxon, $translatableLocale, $categoryName );
                $entityTaxon->addTranslation( $taxonTranslation );
            } else {
                $taxonTranslation   = $entityTaxon->getTranslation( $translatableLocale );
                
                $taxonTranslation->setName( $categoryName );
                $taxonTranslation->setSlug( $this->get( 'vs_application.slug_generator' )->generate( $categoryName ) );
            }
        } else {

            $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
                $this->getParameter( '<?= $taxonomy_code_parameter ?>' )
            );
            
            $taxon   = $this->createTaxon(
                $form['name']->getData(),
                $translatableLocale,
                $entity->getParent() ? $entity->getParent()->getTaxon() : null,
                $taxonomy->getId()
            );
            
            $entity->setTaxon( $taxon );
        }
    }
}
