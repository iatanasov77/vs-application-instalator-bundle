// JqueryUi conflicts with JqueryEasyUi
require( 'jquery-ui-dist/jquery-ui.js' );
require( 'jquery-ui-dist/jquery-ui.css' );
require( 'jquery-ui-dist/jquery-ui.theme.css' );

import { VsTranslator, VsLoadTranslations } from '../includes/bazinga_js_translations.js';
VsLoadTranslations(['VSApplicationBundle']);

var onResourceDeleteOk      = function() {
    $( '#deleteResourceForm' ).submit();
}

var onResourceDeleteCancel  = function() {
    $( '#deleteResourceForm' ).attr( 'action', '' );
    $( '#resource_delete__token' ).val( '' );
    
    $( this ).dialog( "close" );
}

export function VsFormDlete( onOk, onCancel, message = null )
{
    var myButtons           = {};
    var _Translator         = VsTranslator( 'VSApplicationBundle' );
    
    var strMessage          = message ? _Translator.trans( message ) : _Translator.trans( 'vs_application.form.vs_form_delete.message' );
    var translatedDialog    = '<div title="' + _Translator.trans( 'vs_application.form.vs_form_delete.title' ) + '">' + strMessage + '</div>';
    
    myButtons[_Translator.trans( 'vs_application.form.vs_form_delete.btn_ok' )] = onOk;
    myButtons[_Translator.trans( 'vs_application.form.vs_form_delete.btn_cancel' )] = onCancel;
    
    return $( translatedDialog ).dialog( { buttons: myButtons } );
}


/*
 * I'm not sure if this should work. This is an old implementation
 */
$( function()
{
	$( ".btnDeleteResource" ).on( "click", function ( e )
	{
	    e.preventDefault();

	    $( '#deleteResourceForm' ).attr( 'action', $( this ).attr( 'href' ) );
	    $( '#resource_delete__token' ).val( $( this ).attr( 'data-csrftoken' ) );
	    $( '#resource_delete__redirect' ).val( $( this ).attr( 'data-redirectUrl' ) );
	    
	    var message    = $( this ).attr( 'data-message' );
	    var dialog     = VsFormDlete( onResourceDeleteOk, onResourceDeleteCancel, message );
	});
});
