require( '@kanety/jquery-simple-tree-table/dist/jquery-simple-tree-table.js' );
require( '../includes/resource-delete.js' );

$( function()
{
    //$( '#tblCategories' ).simpleTreeTable();
	$( '#tblCategories' ).simpleTreeTable({
		expander: $( '#expander' ),
		collapser: $( '#collapser' ),
        opened: []
	});
	
	$('#collapsed').simpleTreeTable({
        //opened: 'all',
        opened: []
	});
});
