require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );
// Need copy of: jquery-easyui/images/*

require ( 'jquery-duplicate-fields/jquery.duplicateFields.js' );

require( 'jquery-easyui-extensions/EasyuiCombobox.css' );
import { EasyuiCombobox } from 'jquery-easyui-extensions/EasyuiCombobox.js';

$( function()
{
    $( '.attributesContainer' ).duplicateFields({
        btnRemoveSelector: ".btnRemoveField",
        btnAddSelector:    ".btnAddField"
    });
    
    let associationsSelector    = ".product-associations";
    EasyuiCombobox( $( associationsSelector ), {
        required: false,
        multiple: true,
        checkboxId: "product_associations",
        values: null,
        getValuesFrom: 'select-box',
        debug: false
    });
    
    let categorySelector    = "#product_form_categories";
    let selectedCategories  = JSON.parse( $( '#product_form_productCategories').val() );
    EasyuiCombobox( $( categorySelector ), {
        required: true,
        multiple: true,
        checkboxId: "product_category",
        values: selectedCategories,
        debug: false
    });
    
    /*
    var taxonValues = $( '#categoryTaxonIds' ).attr( 'data-values' ).split( ',' );
    $( '#product_form_category_taxon' ).combotree( 'setValues', taxonValues );
    */
    
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
 