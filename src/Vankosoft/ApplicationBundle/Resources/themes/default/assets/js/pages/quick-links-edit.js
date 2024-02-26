import { VsPath } from '../includes/fos_js_routes.js';

$( function()
{
    // bin/console fos:js-routing:dump --format=json --target=public/shared_assets/js/fos_js_routes_admin.json
    $( '#FormContainer' ).on( 'change', '#quick_link_form_locale', function( e ) {
        var quickLinkId  = $( '#FormContainer' ).attr( 'data-itemId' );
        var locale  = $( this ).val();
        
        if ( quickLinkId ) {
            $.ajax({
                type: 'GET',
                url: VsPath( 'vvp_quick_link_form_in_locale', { 'itemId': quickLinkId, 'locale': locale } ),
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
