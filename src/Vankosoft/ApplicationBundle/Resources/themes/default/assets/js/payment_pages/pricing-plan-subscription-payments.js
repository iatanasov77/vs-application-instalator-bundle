// bin/console fos:js-routing:dump --format=json --target=public/shared_assets/js/fos_js_routes_admin.json
import { VsPath } from '../includes/fos_js_routes.js';
import { VsSpinnerShow, VsSpinnerHide } from '../includes/vs_spinner.js';

$( function()
{
    $( '.btnSetPaid' ).on( 'click', function ( e )
    {
        e.preventDefault();
        
        let subscriptionId  = $( this ).attr( 'data-subscriptionId' );
        
        VsSpinnerShow();
        $.ajax({
            type: 'GET',
            url: $( this ).attr( 'href' ),
            success: function ( response ) {
                VsSpinnerHide();
                
                if ( response.status == 'ok' ) {
                    document.location   = VsPath( 'vs_payment_pricing_plan_subscription_show', { 'id': subscriptionId } );
                } else {
                    alert( 'RESPONSE ERROR!!!' );
                }
            }, 
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                VsSpinnerHide();
                
                alert( 'FATAL ERROR!!!' );
            }
        });
    })
});
