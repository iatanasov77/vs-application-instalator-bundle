// JqueryUi conflicts with JqueryEasyUi
require( 'jquery-ui-dist/jquery-ui.js' );
require( 'jquery-ui-dist/jquery-ui.css' );
require( 'jquery-ui-dist/jquery-ui.theme.css' );

require( '@fortawesome/fontawesome-free/css/all.css' );
require( '@fortawesome/fontawesome-free/js/all.js' );
require( '@kanety/jquery-simple-tree-table/dist/jquery-simple-tree-table.js' );

import { VsPath } from '../includes/fos_js_routes.js';
import { VsFormSubmit } from '../includes/vs_form.js';
import { changeOrder, computeNewPosition, changeOrderNew, getInsertAfterId } from '../includes/sortable.js';

const bootstrap = require( 'bootstrap' );

// WORKAROUND: Prevent Double Submiting
global.btnSaveTocPageClicked = window.btnSaveTocPageClicked = false;

$( function ()
{
    $( '#tblTocPages' ).simpleTreeTable({
        expander: $( '#expander' ),
        collapser: $( '#collapser' ),
        opened: []
    });
    
    $( '#collapsed' ).simpleTreeTable({
        //opened: 'all',
        opened: []
    });
    
    $( '#containerTocPages' ).on( 'click', '.btnTocPage', function( e )
    {
        e.preventDefault();
        
        var documentId    = $( this ).attr( 'data-documentId' );
        var tocPageId     = $( this ).attr( 'data-tocPageId' );
        
        $.ajax({
            type: "GET",
            url: VsPath( 'vs_cms_multipage_toc_page_edit', {'documentId': documentId, 'tocPageId': tocPageId} ),
            success: function( response )
            {
                $( '#modalBodyTocPage > div.card-body' ).html( response );
                
                /** Bootstrap 5 Modal Toggle */
                const myModal = new bootstrap.Modal('#multipageTocPageModal', {
                    keyboard: false
                });
                myModal.show( $( '#multipageTocPageModal' ).get( 0 ) );
                
                /**
                 * FIXING THE MODAL/CKEDITOR ISSUE. При мен се случваше само на диалога за Снимка.
                 * --------------------------------------------------------------------------------------
                 * https://stackoverflow.com/questions/19570661/ckeditor-plugin-text-fields-not-editable
                 */
                $( '#multipageTocPageModal' ).removeAttr( "tabindex" );
                $( '#multipageTocPageModal' ).attr( "data-documentId", documentId );
                $( '#multipageTocPageModal' ).attr( "data-tocPageId", tocPageId );
            },
            error: function()
            {
                alert( "SYSTEM ERROR!!!" );
            }
        });
    });
    
    $( '#multipageTocPageModal' ).on( 'change', '#toc_page_form_locale', function( e )
    {
        var documentId  = $( '#multipageTocPageModal' ).attr( 'data-documentId' );
        var tocPageId   = $( '#multipageTocPageModal' ).attr( 'data-tocPageId' );
        var locale      = $( this ).val()
        
        if ( tocPageId ) {
            $.ajax({
                type: 'GET',
                url: VsPath( 'vs_cms_multipage_toc_page_edit', {'documentId': documentId, 'tocPageId': tocPageId, 'locale': locale} ),
                success: function ( response ) {
                    $( '#modalBodyTocPage > div.card-body' ).html( response );
                    $( '#toc_page_form_parent' ).combotree();
                }, 
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert( 'FATAL ERROR!!!' );
                }
            });
        }
    });
    
    $( '#multipageTocPageModal' ).on( 'shown.bs.modal', function ( e )
    {
        $( '#toc_page_form_parent' ).combotree();
    });
    
    $( '#multipageTocPageModal' ).on( 'hide.bs.modal', function ( e )
    {
        $( '#toc_page_form_parent' ).combotree( 'destroy' );
        $( '#modalBodyTocPage > div.card-body' ).html( '' );
    });
    
    $( '#btnSaveTocPage' ).on( 'click', function( e )
    {
        if ( window.btnSaveTocPageClicked ) {
            return;
        }
        window.btnSaveTocPageClicked   = true;
        
        var documentId  = $( '#DocumentFormContainer' ).attr( 'data-documentId' );
        var formData    = new FormData( $( '#form_toc_page' )[ 0 ] );
        var submitUrl   = $( '#form_toc_page' ).attr( 'action' );
        var redirectUrl = VsPath( 'vs_cms_document_update', {'id': documentId} );
        
        var pageText    = CKEDITOR.instances.toc_page_form_text.getData();
        formData.set( "toc_page_form[text]", pageText );
        
        VsFormSubmit( formData, submitUrl, redirectUrl );
    });
    
    let sortableIds;
    $( "#tocPagesTableBody" ).sortable({
        start: function( event, ui ) {
            sortableIds = $( "#tocPagesTableBody" ).sortable( "toArray" );
            //console.log( sortableIds );
        },
        
        update: function( event, ui ) {
            var itemId      = ui.item.attr( "data-node-id" );
            var sortedIDs   = $( "#tocPagesTableBody" ).sortable( "toArray" );
            var itemIndex   = sortedIDs.indexOf( 'tocPage-' + itemId );
            
            var sortedItems = [];
            for ( let i = 0; i < sortedIDs.length; i++ ) {
                sortedItems.push( $( '#' + sortedIDs[i] ).attr( 'data-node-id' ) );
            }
            console.log( sortedIDs );
            console.log( sortedItems );
            //alert( "Position: " + ui.position.top + " Original Position: " + ui.originalPosition.top );
            
            let insertAfterId = getInsertAfterId( itemIndex, sortedItems );
            changeOrderNew( itemId, insertAfterId );
        }
    });
});