require( './tablesortable.css' );

function stripQueryStringAndHashFromPath( url )
{
    return url.split(/[?#]/)[0];
}

$( function()
{
    var sortingKey  = null;
    var sortingDir  = null;
    var tables = document.querySelectorAll( "table.vsTableSortable" );
    var currentUrl  = stripQueryStringAndHashFromPath( document.location.href );
    
    for( var pair of new URLSearchParams( location.search ).entries() ) {
        sortingKey  = pair[0];
        sortingDir  = pair[1];
    }
    
    for ( i = 0; i < tables.length; i++ ) {
        table = tables[i];
    
        if ( thead = table.querySelector("thead" ) ) {
            headers = thead.querySelectorAll( "th" );

            for ( j = 0; j < headers.length; j++ ) {
                if ( $( headers[j] ).attr( 'data-sortable' ) == "true" ) {
                    var $sortBy     = $( headers[j] ).attr( 'data-field' );
                    var $sortDir    = 'asc';
                    if ( sortingKey == 'sorting[' + $sortBy + ']' ) {
                        $sortDir    = sortingDir == 'desc' ? 'asc' : 'desc';
                    }
                    
                    /**
                     * Using URL Encode Characters That Used In Sylius Resource Sorting
                     * -----------------------------------------------------------------
                     * https://stackoverflow.com/questions/9966053/what-does-5b-and-5d-in-post-requests-stand-for
                     * https://en.wikipedia.org/wiki/Percent-encoding
                     */
                    var header  = '<a href="' + currentUrl + '?sorting%5B' + $sortBy + '%5D=' + $sortDir + '">' + headers[j].innerText + '</a>'
                    if ( sortingKey == 'sorting[' + $sortBy + ']' ) {
                        if ( sortingDir == 'asc' ) {
                            header      += '&nbsp;<i class="fas fa-arrow-down"></i>';
                        } else {
                            header      += '&nbsp;<i class="fas fa-arrow-up"></i>';
                        }
                    }
                    
                    headers[j].innerHTML    = header;
                } else {
                    headers[j].innerHTML    = "<a href='" + currentUrl + "#'>" + headers[j].innerText + "</a>";
                }
            }
        }
    }
});
