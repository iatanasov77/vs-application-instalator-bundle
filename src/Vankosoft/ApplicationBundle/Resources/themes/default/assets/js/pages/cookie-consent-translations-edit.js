$( function()
{
    let availableLocalesJson    = $( "<textarea/>" ).html( $( '#cookie_consent_translation_form_availableLocales' ).val() ).text();
    let availableLocales        = JSON.parse( availableLocalesJson );
    //console.log( availableLocales );
    
	$( '#cookie_consent_translation_form_languageCode' ).on( 'change', function ( e ) {
	    $( '#cookie_consent_translation_form_localeCode' ).val( availableLocales[$( this ).val()] );
	});
});
