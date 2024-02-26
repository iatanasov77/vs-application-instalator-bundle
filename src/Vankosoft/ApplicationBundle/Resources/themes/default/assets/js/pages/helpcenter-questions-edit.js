import { VsPath } from '../includes/fos_js_routes.js';

$( function()
{
    // bin/console fos:js-routing:dump --format=json --target=public/shared_assets/js/fos_js_routes_admin.json
    $( '#FormContainer' ).on( 'change', '#help_center_question_form_locale', function( e ) {
        var questionId  = $( '#FormContainer' ).attr( 'data-itemId' );
        var locale  = $( this ).val();
        
        if ( questionId ) {
            $.ajax({
                type: 'GET',
                url: VsPath( 'vvp_help_center_form_in_locale', { 'itemId': questionId, 'locale': locale } ),
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
