import { getFileName } from '../includes/file-input.js';

$( function()
{
    $( '#profile_form_profilePicture' ).on( 'change', function( e )
    {
        var newFilename = getFileName( $( this ) );
        
        $( '#profilePictureName' ).text( newFilename );
    });
});
