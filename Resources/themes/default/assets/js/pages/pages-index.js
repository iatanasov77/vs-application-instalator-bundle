require( '../includes/clone_preview.js' );
require( '../includes/resource-delete.js' );

import { VsPath } from '../includes/fos_js_routes.js';

$( function()
{
	$( "#form_filterByCategory" ).on( 'change', function() {
        let filterCategory  = $( this ).val();
        let url = VsPath( 'vs_cms_pages_index' );
        
        if ( filterCategory ) {
            url = VsPath( 'vs_cms_pages_index_filtered', { 'filterCategory': filterCategory } );
        }
        
        document.location   = url;
    });
});
