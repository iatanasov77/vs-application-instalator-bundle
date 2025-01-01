/**
 * May be Wrong import of packages but for fast fix can use it
 */
export function VsRemoveDuplicates()
{
    var seen    = {};
    var i       = 0;
    $( 'div.form-group' ).each( function()
    {
        seen[i] = {
            'span.textbox.combo':       false,
            'div.bootstrap-tagsinput':  false
        };
        
        $( 'span.textbox.combo' ).each( function()
        {
            if ( seen[i]['span.textbox.combo'] )
                $( this ).remove();
            else
                seen[i]['span.textbox.combo'] = true;
        });
        
        $( 'div.bootstrap-tagsinput' ).each( function()
        {
            if ( seen[i]['div.bootstrap-tagsinput'] )
                $( this ).remove();
            else
                seen[i]['div.bootstrap-tagsinput'] = true;
        });
        
        i++;
    });
}
