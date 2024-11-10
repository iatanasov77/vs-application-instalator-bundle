require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );

$( function ()
{
    var hash = location.hash.replace( /^#/, '' );
    if ( hash ) {
        var someVarName = $( '.nav-tabs a[href="#' + hash + '"]' );
        var tab         = new bootstrap.Tab( someVarName );
        tab.show();
    }
    
    $( '.nav-tabs a' ).on( 'shown.bs.tab', function ( e )
    {
        window.location.hash = e.target.hash;
        window.scrollTo( 0, 0 );
    });
    
    
});
