import { getFileName } from '../includes/bootstrap-5/file-input.js';

$( function()
{
    $( '#profile_form_profilePicture' ).on( 'change', function( e )
    {
        var newFilename = getFileName( $( this ) );
        
        $( '#profilePictureName' ).text( newFilename );
    });
});
