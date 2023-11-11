export function VsFormSubmit( formData, submitUrl, redirectUrl )
{
    $.ajax({
        type: 'POST',
        url: submitUrl,
        data: formData,
        success: function ( response ) {
            if ( response.status == 'ok' ) {
                document.location = redirectUrl;
                
            } else {
                window.dispatchEvent(
                    new CustomEvent( "VsFormSubmitError", {
                        detail: {
                            message: response.message
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

$( function()
{
    window.addEventListener( 'VsFormSubmitError', event => {
        alert( 'VsFormSubmit Error: ' + event.detail.message );
    });
});
