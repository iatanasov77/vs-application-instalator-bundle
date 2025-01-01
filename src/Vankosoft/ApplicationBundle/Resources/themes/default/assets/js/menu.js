$( function()
{
    $( '.main-menu-current-item' ).addClass( 'active' );
    $( '.main-menu-current-item' ).closest( '.submenu' ).addClass( 'show' );
    
    var activePosition  = $( '.main-menu-current-item' ).position();
    if( ! activePosition ) {
        var activeItemPath  = $( 'ol.breadcrumb' ).children( 'li.breadcrumb-item' ).eq( 1 ).find( 'a' ).attr( 'href' );
        var currentElement  = $( 'a.nav-link[href="' + activeItemPath + '"]' )
        
        if ( currentElement ) {
            activePosition      = currentElement.position();
        
            currentElement.addClass( 'active' );
            currentElement.closest( '.submenu' ).addClass( 'show' );
        }
    }
    
    if ( activePosition ) {
        $( '.menu-list' ).slimScroll({
            scrollTo: ( activePosition.top - 50 ) + 'px',
        });
    }
});
