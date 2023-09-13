/**
 *  Fix Bootstrap Dropdowns on Custom Entries
 */

var bootstrap               = require( 'bootstrap' );
window.btnProfileClicked    = false;

$( function()
{
    let toggleEl   = $( '#navbarDropdownMenuLink2' );
    toggleEl.on( 'click', function( e )
    {
        let toggleBtn   = toggleEl[0];
        let dropdownEl  = new bootstrap.Dropdown( toggleBtn );
        
        let profileMenu = toggleEl.next( '.dropdown-menu' );
        if ( profileMenu.hasClass( 'show' ) ) {
            if ( window.btnProfileClicked ) {
                window.btnProfileClicked   = false;
                return false;  
            }
            
            //alert( 'Displayed' );
            dropdownEl.toggle();
        } else {
            //alert( 'NOT Displayed' );
            dropdownEl.show();
            window.btnProfileClicked   = true;
        }
    });
});
