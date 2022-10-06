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
        $formPost   = $request->request->get( '<?= $form_name ?>' );
        
        if ( isset( $formPost['locale'] ) ) {
            $entity->setTranslatableLocale( $formPost['locale'] );
        }
    }
}
