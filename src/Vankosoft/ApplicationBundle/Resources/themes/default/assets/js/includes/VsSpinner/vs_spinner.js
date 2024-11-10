require( './vs_spinner.css' );

const spinner   = '<div class="VsSpinner"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

export function VsSpinnerShow( targetId )
{
    $( '#' + targetId ).addClass( 'VsSpinnerTarget' );
    $( '#' + targetId ).after( spinner );
}

export function VsSpinnerHide( targetId )
{
    $( '#' + targetId ).next( '.VsSpinner' ).remove();
    $( '#' + targetId ).removeClass( 'VsSpinnerTarget' );
}
