export function VsFormSubmit( formData, submitUrl, redirectUrl )
{
    $.ajax({
        type: 'POST',
        url: submitUrl,
        data: formData,
        success: function ( response ) {
            if ( response.status == 'ok' ) {
                if ( redirectUrl ) {
                    document.location = redirectUrl;
                }
            } else {
                window.dispatchEvent(
                    // Response Can to Be Form With Errors
                    new CustomEvent( "VsFormSubmitError", {
                        detail: {
                            message: response.message ? response.message : response;
                        },
                    })
                );
            }
        }, 
        error: function( XMLHttpRequest, textStatus, errorThrown ) {
            alert( 'FATAL ERROR!!!' );
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

export function VsGetSubmitButton()
{
    var clickedName = $( 'input[type=submit][clicked=true]' ).attr( 'name' );
    if ( clickedName == undefined ) {
        clickedName = $( 'button[type=submit][clicked=true]' ).attr( 'name' );
    }
    
    return clickedName;
}

$( function()
{
    window.addEventListener( 'VsFormSubmitError', event => {
        if ( event.detail.message ) {
            alert( 'VsFormSubmit Error: ' + event.detail.message );
        }
    });
    
    $( 'form input[type=submit]' ).on( 'click', function()
    {
        $( 'input[type=submit]', $( this ).parents( 'form' ) ).removeAttr( 'clicked' );
        $( this ).attr( 'clicked', 'true' );
    });
    
    $( 'form button[type=submit]' ).on( 'click', function()
    {
        $( 'input[type=submit]', $( this ).parents( 'form' ) ).removeAttr( 'clicked' );
        $( this ).attr( 'clicked', 'true' );
    });
});
