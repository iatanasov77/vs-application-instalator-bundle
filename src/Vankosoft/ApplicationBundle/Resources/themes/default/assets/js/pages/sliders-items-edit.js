require( '../includes/bootstrap-5/file-input.js' );
import { VsPath } from '../includes/fos_js_routes.js';

$( function()
{
    // bin/console fos:js-routing:dump --format=json --target=public/shared_assets/js/fos_js_routes_admin.json
    $( '#FormContainer' ).on( 'change', '#slider_form_locale', function( e ) {
        var sliderId  = $( '#FormContainer' ).attr( 'data-itemId' );
        var locale  = $( this ).val();
        
        if ( sliderId ) {
            $.ajax({
                type: 'GET',
                url: VsPath( 'vvp_slider_form_in_locale', { 'itemId': sliderId, 'locale': locale } ),
                success: function ( data ) {
                    $( '#FormContainer' ).html( data );
                }, 
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert( 'FATAL ERROR!!!' );
                }
            });
        }
    });
});
