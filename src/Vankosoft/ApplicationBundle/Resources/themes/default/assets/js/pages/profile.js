/*
require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );
*/

import { VsDisplayPassword } from '../includes/password-generator.js';
import { VsPath } from '../includes/fos_js_routes.js';
import { VsTranslator, VsLoadTranslations } from '../includes/bazinga_js_translations.js';
VsLoadTranslations(['VSApplicationBundle']);

require( '../includes/bootstrap-5/file-input.js' );
const bootstrap = require( 'bootstrap' );

$( function()
{
    $( '#btnGeneratePassword' ).on( 'click', function ( e )
    {
        $.ajax({
            type: 'GET',
            url: VsPath( 'vs_application_json_get_passwords', { 'quantity': 1 } ),
            success: function ( data )
            {
                if ( data['status'] == 'ok' ) {
                    var password    = data['data']['passwords'][0];
                    
                    $( '#change_password_form_password_first' ).val( password );
                    $( '#change_password_form_password_second' ).val( password );
                    
                    var dialog  = VsDisplayPassword( password );
                } else {
                    alert( 'ERROR !!!' );
                }
            }, 
            error: function( XMLHttpRequest, textStatus, errorThrown )
            {
                alert( 'ERROR !!!' );
            }
        });
    });
    
    var hash = location.hash.replace( /^#/, '' );
    if ( hash ) {
        var someVarName = $( '.nav-tabs a[href="#' + hash + '"]' );
        var tab         = new bootstrap.Tab( someVarName );
        tab.show();
    }
    
    $( '.nav-tabs a' ).on( 'shown.bs.tab', function ( e )
    {
        window.location.hash = e.target.hash;
        window.scrollTo( 0, 0 );
    });
    
    $( '#btnSetAllNotificationsReaded' ).on( 'click', function ( e )
    {
    
    });
    
    $( '.btnShowNotification' ).on( 'click', function ( e )
    {
        let hasBody = $( this ).attr( 'data-notificationHasBody' );
        console.log( hasBody );
    });
});
