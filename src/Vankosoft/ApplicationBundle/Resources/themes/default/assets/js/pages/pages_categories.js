require( '@kanety/jquery-simple-tree-table/dist/jquery-simple-tree-table.js' );

$( function()
{
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
