require( '@kanety/jquery-simple-tree-table/dist/jquery-simple-tree-table.js' );
require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );

/**
 * Sortable Is Not Implemented. If Needed For Example See 'toc-pages.js'
 */
$( function()
{
  	$( '#tableTaxons' ).simpleTreeTable({
        expander: $( '#expander' ),
        collapser: $( '#collapser' ),
        opened: []
    });
    
    $( '#collapsed' ).simpleTreeTable({
        //opened: 'all',
        opened: []
    });
});
