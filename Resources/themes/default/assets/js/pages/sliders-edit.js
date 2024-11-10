require( '../includes/resource-delete.js' );
require( '../includes/bootstrap-5/file-input.js' );

import { VsPath } from '../includes/fos_js_routes.js';
import { VsTranslator, VsLoadTranslations } from '../includes/bazinga_js_translations.js';
VsLoadTranslations(['VSCmsBundle']);

import { VsFormSubmit } from '../includes/vs_form.js';

import VsSortable from '../includes/sortable';
const siSortable  = new VsSortable( 'vs_cms_slider_item_ext_sort_action' );

// WORKAROUND: Prevent Double Submiting
global.btnSaveSliderItemClicked = window.btnSaveSliderItemClicked = false;

function initSliderItemPhotoField()
{
    $( '#sliderItemModal' ).on( 'change', 'div.form-field-file input[type=file]', function()
    {
        var label       = $( this ).next();
        var fileName    = $( this ).val().split( '\\' ).pop();
        if ( fileName ) { 
            $( label ).html( fileName );
        } else { 
            $( label ).html( '' );
        }
    });
}

$( function()
{
    // bin/console fos:js-routing:dump --format=json --target=public/shared_assets/js/fos_js_routes_admin.json
    $( '#FormContainer' ).on( 'change', '#slider_form_locale', function( e ) {
        var sliderId  = $( '#FormContainer' ).attr( 'data-itemId' );
        var locale  = $( this ).val();
        
        if ( sliderId ) {
            $.ajax({
                type: 'GET',
                url: VsPath( 'vvp_slider_form_in_locale', { 'itemId': sliderId, 'locale': locale } ),
                success: function ( data ) {
                    $( '#FormContainer' ).html( data );
                }, 
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert( 'FATAL ERROR!!!' );
                }
            });
        }
    });
    
    $( '#containerSliderItems' ).on( 'click', '.btnSliderItem', function( e )
    {
        e.preventDefault();
        
        var sliderId    = $( this ).attr( 'data-sliderId' );
        var itemId      = $( this ).attr( 'data-itemId' );
        var _Translator = VsTranslator( 'VSCmsBundle' );
        
        $.ajax({
            type: "GET",
            url: VsPath( 'vs_cms_slider_item_ext_edit', {'sliderId': sliderId, 'itemId': itemId} ),
            success: function( response )
            {
                let modalTitle  = itemId == '0' ?
                                    _Translator.trans( 'vs_cms.modal.slider_item.create_title' ) :
                                    _Translator.trans( 'vs_cms.modal.slider_item.update_title' );
                                    
                $( '#modalTitle' ).text( modalTitle );
                $( '#modalBodySliderItem > div.card-body' ).html( response );
                
                
                /** Bootstrap 5 Modal Toggle */
                const myModal = new bootstrap.Modal('#sliderItemModal', {
                    keyboard: false
                });
                myModal.show( $( '#sliderItemModal' ).get( 0 ) );
                
                /**
                 * FIXING THE MODAL/CKEDITOR ISSUE. При мен се случваше само на диалога за Снимка.
                 * --------------------------------------------------------------------------------------
                 * https://stackoverflow.com/questions/19570661/ckeditor-plugin-text-fields-not-editable
                 */
                $( '#sliderItemModal' ).removeAttr( "tabindex" );
                
                $( '#sliderItemModal' ).attr( "data-sliderId", sliderId );
                $( '#sliderItemModal' ).attr( "data-itemId", itemId );
            },
            error: function()
            {
                alert( "SYSTEM ERROR!!!" );
            }
        });
    });
    
    $( '#sliderItemModal' ).on( 'change', '#slider_item_form_locale', function( e )
    {
        var sliderId    = parseInt( $( '#sliderItemModal' ).attr( 'data-sliderId' ) );
        var itemId      = parseInt( $( '#sliderItemModal' ).attr( 'data-itemId' ) );
        var locale      = $( this ).val()
        
        if ( itemId ) {
            $.ajax({
                type: 'GET',
                url: VsPath( 'vs_cms_slider_item_ext_edit', {'sliderId': sliderId, 'itemId': itemId, 'locale': locale} ),
                success: function ( response ) {
                    $( '#modalBodySliderItem > div.card-body' ).html( response );
                }, 
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert( 'FATAL ERROR!!!' );
                }
            });
        }
    });
    
    $( '#btnSaveSliderItem' ).on( 'click', function( e )
    {
        if ( window.btnSaveSliderItemClicked ) {
            return;
        }
        window.btnSaveSliderItemClicked = true;
        
        var sliderId    = $( '#SliderFormContainer' ).attr( 'data-sliderId' );
        var formData    = new FormData( $( '#FormSliderItem' )[ 0 ] );
        var submitUrl   = $( '#FormSliderItem' ).attr( 'action' );
        var redirectUrl = VsPath( 'vs_cms_slider_update', {'id': sliderId} );
        
        var description = CKEDITOR.instances.slider_item_form_description.getData();
        formData.set( "slider_item_form[description]", description );
        
        VsFormSubmit( formData, submitUrl, redirectUrl );
    });
    
    let sortableIds;
    $( "#sliderItemsTableBody" ).sortable({
        start: function( event, ui ) {
            sortableIds = $( "#sliderItemsTableBody" ).sortable( "toArray" );
            //console.log( sortableIds );
        },
        
        update: function( event, ui ) {
            var itemId      = ui.item.attr( "data-node-id" );
            var sortedIDs   = $( "#sliderItemsTableBody" ).sortable( "toArray" );
            var itemIndex   = sortedIDs.indexOf( 'sliderItem-' + itemId );
            
            var sortedItems = [];
            for ( let i = 0; i < sortedIDs.length; i++ ) {
                sortedItems.push( $( '#' + sortedIDs[i] ).attr( 'data-node-id' ) );
            }
            //console.log( sortedIDs );
            //console.log( sortedItems );
            //alert( "Position: " + ui.position.top + " Original Position: " + ui.originalPosition.top );
            
            let insertAfterId = siSortable.getInsertAfterId( itemIndex, sortedItems );
            siSortable.changeOrderNew( itemId, insertAfterId );
        }
    });
    
    initSliderItemPhotoField();
});
