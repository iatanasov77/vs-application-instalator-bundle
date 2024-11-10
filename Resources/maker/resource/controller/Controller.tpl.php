<?= "<?php" ?> namespace <?= $namespace ?>;<?= "\n" ?>

<?= $use_statements; ?>

class <?= $class_name ?> extends AbstractCrudController
{
    protected function customData( Request $request, $entity = NULL ): array
    {
    	return [
    	
    	];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $locale	= $request->request->get( 'locale' );
        
        if ( $locale ) {
            $entity->setTranslatableLocale( $locale );
        }
    }
}
