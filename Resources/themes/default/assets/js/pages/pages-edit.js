require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );
// Need copy of: jquery-easyui/images/*

require( '../includes/clone_preview.js' );
import { VsPath } from '../includes/fos_js_routes.js';

import Tagify from '@yaireo/tagify';
import '@yaireo/tagify/dist/tagify.css';

import DragSort from '@yaireo/dragsort';
import '@yaireo/dragsort/dist/dragsort.css';

var tagsInput;
var tagify;
var dragsort;

// must update Tagify's value according to the re-ordered nodes in the DOM
function onDragEnd( elm )
{
    tagify.updateValueByDOMTags();
}

function initForm()
{
    $( '#page_form_category_taxon' ).combotree();
    
    var tagsInputWhitelist  = $( '#page_form_tagsInputWhitelist' ).val().split( ',' );
    //console.log( tagsInputWhitelist );
    
    tagsInput   = $( '#page_form_tags' )[0];
    tagify      = new Tagify( tagsInput, {
        whitelist : tagsInputWhitelist,
        dropdown : {
            classname     : "color-blue",
            enabled       : 0,              // show the dropdown immediately on focus
            maxItems      : 5,
            position      : "text",         // place the dropdown near the typed text
            closeOnSelect : false,          // keep the dropdown open after selecting a suggestion
            highlightFirst: true
        }
    });
    
    // bind "DragSort" to Tagify's main element and tell
    // it that all the items with the below "selector" are "draggable"
    dragsort    = new DragSort( tagify.DOM.scope, {
        selector: '.'+tagify.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEnd
        }
    }); 
}

$( function()
{
    initForm();
    
    // bin/console fos:js-routing:dump --format=json --target=public/shared_assets/js/fos_js_routes_admin.json
	$( '#FormContainer' ).on( 'change', '#page_form_locale', function( e ) {
		var pageId	= $( '#FormContainer' ).attr( 'data-itemId' );
		var locale	= $( this ).val()
		
		if ( pageId ) {
    		$.ajax({
                type: 'GET',
                url: VsPath( 'vs_cms_pages_form_in_locale', { 'itemId': pageId, 'locale': locale } ),
                success: function ( data ) {
                    $( '#FormContainer' ).html( data );
                    initForm();
                }, 
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert( 'FATAL ERROR!!!' );
                }
            });
        }
    });
    
    /**
     * Submit Checked Roles Tree
     */
    $( 'form[name="page_form"]' ).on( 'submit', function ( e )
    {
        let treeModule  = require( '../includes/tree.js' );
        var element = treeModule.createCheckedTreeElement(
            "selectedCategories",
            $( '#page_form_category_taxon' ).combotree( 'tree' ).tree( 'getChecked' )
        );
        $( this ).append( element );
    });
});
 