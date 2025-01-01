import './file-input.css';

$( function()
{
    $( 'div.form-field-file input[type=file]' ).on( 'change', function()
    {
        var label       = $( this ).next();
        var fileName    = $( this ).val().split( '\\' ).pop();
        if ( fileName ) { 
            $( label ).html( fileName );
        } else { 
            $( label ).html( '' );
        }
    });
});
