import { VsPath } from '../includes/fos_js_routes.js';

$( function()
{
    $( '.btnSetPaid' ).on( 'click', function ( e )
    {
        e.preventDefault();
        
        let subscriptionId  = $( this ).atrr( 'data-subscriptionId' );
        
        $.ajax({
            type: 'GET',
            url: $( this ).attr( 'href' ),
            success: function ( response ) {
                if ( response.status == 'ok' ) {
                    document.location   = VsPath( 'vs_payment_pricing_plan_subscription_show', { 'id': subscriptionId } );
                } else {
                    alert( 'RESPONSE ERROR!!!' );
                }
            }, 
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert( 'FATAL ERROR!!!' );
            }
        });
    })
});
