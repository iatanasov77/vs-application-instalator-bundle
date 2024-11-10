/*
require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );
*/

import { VsDisplayPassword } from '../includes/password-generator.js';
import { VsPath } from '../includes/fos_js_routes.js';
import { VsTranslator, VsLoadTranslations } from '../includes/bazinga_js_translations.js';
VsLoadTranslations(['VSApplicationBundle']);

require( '../includes/bootstrap-5/file-input.js' );

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
    
    $( '#btnClearAllActivities' ).on( 'click', function ( e )
    {
        $.ajax({
            type: 'GET',
            url: VsPath( 'vs_users_activities_clear_all' ),
            success: function ( data )
            {
                window.location.reload();
            }, 
            error: function( XMLHttpRequest, textStatus, errorThrown )
            {
                alert( 'ERROR !!!' );
            }
        });
    });
    
    $( '#btnClearAllNotifications' ).on( 'click', function ( e )
    {
        $.ajax({
            type: 'GET',
            url: VsPath( 'vs_users_notifications_clear_all' ),
            success: function ( data )
            {
                window.location.reload();
            }, 
            error: function( XMLHttpRequest, textStatus, errorThrown )
            {
                alert( 'ERROR !!!' );
            }
        });
    });
    
    $( '#btnSetAllNotificationsReaded' ).on( 'click', function ( e )
    {
        $.ajax({
            type: 'GET',
            url: VsPath( 'vs_users_notifications_set_all_readed' ),
            success: function ( data )
            {
                window.location.reload();
            }, 
            error: function( XMLHttpRequest, textStatus, errorThrown )
            {
                alert( 'ERROR !!!' );
            }
        });
    });
    
    $( '.btnShowNotification' ).on( 'click', function ( e )
    {
        let hasBody = $( this ).attr( 'data-notificationHasBody' );
        if ( Boolean( hasBody ) ) {
            let notificationId = $( this ).attr( 'data-notificationId' );
            
            $.ajax({
                type: 'GET',
                url: VsPath( 'vs_users_notifications_show', { 'id': notificationId } ),
                success: function ( data )
                {
                    $( '#notificationShow > div.card-body' ).html( data.response );
                    
                    /** Bootstrap 5 Modal Toggle */
                    const myModal = new bootstrap.Modal('#notification-show-modal', {
                        keyboard: false
                    });
                    myModal.show( $( '#notification-show-modal' ).get( 0 ) );
                }, 
                error: function( XMLHttpRequest, textStatus, errorThrown )
                {
                    alert( 'ERROR !!!' );
                }
            });
            
        }
    });
    
    $( '#notification-show-modal' ).on( 'hide.bs.modal', function ( e )
    {
        window.location.reload();
    });
});
