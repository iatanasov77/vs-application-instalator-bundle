$( function()
{
    $( '.main-menu-current-item' ).addClass( 'active' );
    $( '.main-menu-current-item' ).closest( '.submenu' ).addClass( 'show' );
    
    var activePosition  = $( '.main-menu-current-item' ).position();
    if( ! activePosition ) {
        var activeItemPath  = $( 'ol.breadcrumb' ).children( 'li.breadcrumb-item' ).eq( 1 ).find( 'a' ).attr( 'href' );
        activePosition      = $( 'a.nav-link[href="' + activeItemPath + '"]' ).position();
        
        alert( activeItemPath );
    }
    
    if ( activePosition ) {
        $( '.menu-list' ).slimScroll({
            scrollTo: ( activePosition.top - 50 ) + 'px',
        });
    }
});
