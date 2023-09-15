require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );
// Need copy of: jquery-easyui/images/*

require( '../includes/clone_preview.js' );
import { VsPath } from '../includes/fos_js_routes.js';

$( function()
{
    // bin/console fos:js-routing:dump --format=json --target=public/shared_assets/js/fos_js_routes_admin.json
	$( '#FormContainer' ).on( 'change', '#page_form_locale', function( e ) {
		var pageId	= $( '#FormContainer' ).attr( 'data-itemId' );
		var locale	= $( this ).val()
		
		if ( pageId ) {
    		$.ajax({
                type: 'GET',
                url: VsPath( 'vs_cms_pages_form_in_locale', { 'itemId': pageId, 'locale': locale } ),
                success: function ( data ) {
                    $( '#FormContainer' ).html( data );
                    $( '#page_form_category_taxon' ).combotree();
                }, 
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert( 'FATAL ERROR!!!' );
                }
            });
        }
    });
});
 