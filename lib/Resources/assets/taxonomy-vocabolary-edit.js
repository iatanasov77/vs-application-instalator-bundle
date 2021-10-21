// https://symfonycasts.com/screencast/webpack-encore-legacy/require-css
require( 'bootstrap-gtreetable/dist/bootstrap-gtreetable.css' );
require( 'bootstrap-gtreetable/dist/bootstrap-gtreetable.js' );

$( function()
{
//	$( '.termsContainer' ).duplicateFields({
//        btnRemoveSelector: ".btnRemoveTerm",
//        btnAddSelector:    ".btnAddTerm"
//    });
	
	$( '#gtreetable' ).gtreetable({
	  'source': function ( id ) {
	      return {
	        type: 'GET',
	        url: $( '#gtreetable' ).attr( 'data-url' ),
	        data: { 'id': id },        
	        dataType: 'json',
	        error: function( XMLHttpRequest ) {
	          alert( XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText );
	        }
	      }
	    },
	    'types': { default: 'glyphicon glyphicon-folder-open'},
	    'language': 'en'
	});
});
