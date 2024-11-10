export function VsSpinnerShow()
{
    $( '.VsSpinnerOverlay' ).addClass( 'VsSpinnerOverlayFadeout' );
    $( '.VsSpinner' ).show();
}

export function VsSpinnerHide()
{
    $( '.VsSpinner' ).hide();
    $( '.VsSpinnerOverlay' ).removeClass( 'VsSpinnerOverlayFadeout' );
}
