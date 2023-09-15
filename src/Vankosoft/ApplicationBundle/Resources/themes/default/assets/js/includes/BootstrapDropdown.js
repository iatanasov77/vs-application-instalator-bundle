/**
 *  Fix Bootstrap Dropdowns on Custom Entries
 */

var bootstrap                   = require( 'bootstrap' );
window.btnProfileClicked        = false;
window.btnNotificationsClicked  = false;

$( function()
{
    let toggleProfileEl   = $( '#navbarDropdownMenuLink2' );
    toggleProfileEl.on( 'click', function( e )
    {
        let toggleBtn   = toggleProfileEl[0];
        let dropdownEl  = new bootstrap.Dropdown( toggleBtn );
        
        let profileMenu = toggleProfileEl.next( '.dropdown-menu' );
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
    
    let toggleNotificationsEl   = $( '#navbarDropdownMenuLink1' );
    toggleNotificationsEl.on( 'click', function( e )
    {
        let toggleBtn   = toggleNotificationsEl[0];
        let dropdownEl  = new bootstrap.Dropdown( toggleBtn );
        
        let notificationsMenu = toggleNotificationsEl.next( '.dropdown-menu' );
        if ( notificationsMenu.hasClass( 'show' ) ) {
            if ( window.btnNotificationsClicked ) {
                window.btnNotificationsClicked  = false;
                return false;  
            }
            
            //alert( 'Displayed' );
            dropdownEl.toggle();
        } else {
            //alert( 'NOT Displayed' );
            dropdownEl.show();
            window.btnNotificationsClicked   = true;
        }
    });
});
