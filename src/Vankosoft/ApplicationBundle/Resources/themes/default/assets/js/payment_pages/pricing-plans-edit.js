require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );
// Need copy of: jquery-easyui/images/*

require ( 'jquery-duplicate-fields/jquery.duplicateFields.js' );

$( function()
{
    $( '#paidServicesContainer' ).duplicateFields({
        btnRemoveSelector: ".btnRemoveField",
        btnAddSelector:    ".btnAddField"
    });
    
    var taxonValues = $( '#categoryTaxonIds' ).attr( 'data-values' ).split( ',' );
    $( '#product_form_category_taxon' ).combotree( 'setValues', taxonValues );
    
	$( '#page_form_locale' ).on( 'change', function( e ) {
		var pageId	= $( '#pageFormContainer' ).attr( 'data-pageId' );
		var locale	= $( this ).val()
		
		if ( pageId ) {
    		$.ajax({
                type: 'GET',
                url: '/page-actions/get-form/' + locale + '/' + pageId,
                success: function ( data ) {
                    $( '#pageFormContainer' ).html( data );
                    $( '#page_form_category_taxon' ).combotree();
                }, 
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert( 'FATAL ERROR!!!' );
                }
            });
        }
    });
});
 