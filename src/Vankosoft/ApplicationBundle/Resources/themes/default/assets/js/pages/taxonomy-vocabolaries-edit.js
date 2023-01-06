//require( 'bootstrap-gtreetable/dist/bootstrap-gtreetable.css' );
//require( 'bootstrap-gtreetable/dist/bootstrap-gtreetable.js' );
require( '@kanety/jquery-simple-tree-table/dist/jquery-simple-tree-table.js' );
require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );

$( function()
{
/*
	$( '#tableTaxons' ).gtreetable({
		'source': function ( id ) {
			return {
				type: 'GET',
				url: $( '#tableTaxons' ).attr( 'data-url' ),
				data: { 'parentTaxonId': id },
				dataType: 'json',
				error: function( XMLHttpRequest ) {
					alert( 'GTreeTable ERROR !!!' );
					alert( XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText );
				}
  	      	}
  	    }
  	});
*/
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
