require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );

$( function()
{
    /**
     * Submit Checked Roles Tree
     */
    $( 'form[name="widget_form"]' ).on( 'submit', function ( e )
    {
        let treeModule  = require( '../includes/tree.js' );
        var element = treeModule.createCheckedTreeElement(
            "selectedRoles",
            $( '#widget_form_allowedRoles' ).combotree( 'tree' ).tree( 'getChecked' )
        );
        $( this ).append( element );
    });
});