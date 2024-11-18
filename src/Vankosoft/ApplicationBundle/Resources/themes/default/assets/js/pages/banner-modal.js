require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );

require( '../includes/bootstrap-5/file-input.js' );
import { VsPath } from '../includes/fos_js_routes.js';
import { EasyuiCombobox } from 'jquery-easyui-extensions/EasyuiCombobox.js';
import { VsRemoveDuplicates } from '@/js/includes/vs_remove_duplicates.js';
import { VsFormSubmit } from '../includes/vs_form.js';
import { VsTranslator, VsLoadTranslations } from '../includes/bazinga_js_translations.js';
VsLoadTranslations(['VSCmsBundle']);

// WORKAROUND: Prevent Double Submiting
global.btnSaveBannerClicked = window.btnSaveBannerClicked = false;

function initBannerPlacesCombo()
{
    let selectedPlaces  = JSON.parse( $( '#banner_form_selectedPlaces' ).val() );
    if ( ! selectedPlaces.length ) {
        selectedPlaces    = null;
    }
    
    EasyuiCombobox( $( '#banner_form_places' ), {
        required: true,
        multiple: true,
        checkboxId: "BannerPlaces",
        values: selectedPlaces
    });
}

function initBannerImageField()
{
    $( '#bannerModal' ).on( 'change', 'div.form-field-file input[type=file]', function()
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
    $( '#containerBanners' ).on( 'click', '.btnBanner', function( e )
    {
        e.preventDefault();
        
        var placeId     = $( this ).attr( 'data-placeId' );
        var itemId      = $( this ).attr( 'data-itemId' );
        var _Translator = VsTranslator( 'VSCmsBundle' );
        
        $.ajax({
            type: "GET",
            url: VsPath( 'vs_cms_banner_ext_edit', {'placeId': placeId, 'itemId': itemId} ),
            success: function( response )
            {
                let modalTitle  = itemId == '0' ?
                                    _Translator.trans( 'vs_cms.modal.banner.create_title' ) :
                                    _Translator.trans( 'vs_cms.modal.banner.update_title' );
                                    
                $( '#modalTitle' ).text( modalTitle );
                $( '#modalBodyBanner > div.card-body' ).html( response );
                initBannerPlacesCombo();
                
                /** Bootstrap 5 Modal Toggle */
                const myModal = new bootstrap.Modal('#bannerModal', {
                    keyboard: false
                });
                myModal.show( $( '#bannerModal' ).get( 0 ) );
                
                /**
                 * FIXING THE MODAL/CKEDITOR ISSUE. При мен се случваше само на диалога за Снимка.
                 * --------------------------------------------------------------------------------------
                 * https://stackoverflow.com/questions/19570661/ckeditor-plugin-text-fields-not-editable
                 */
                $( '#bannerModal' ).removeAttr( "tabindex" );
                
                $( '#bannerModal' ).attr( "data-placeId", placeId );
                $( '#bannerModal' ).attr( "data-itemId", itemId );
            },
            error: function()
            {
                alert( "SYSTEM ERROR!!!" );
            }
        });
    });
    
    $( '#bannerModal' ).on( 'change', '#banner_form_locale', function( e )
    {
        var placeId = parseInt( $( '#bannerModal' ).attr( 'data-placeId' ) );
        var itemId  = parseInt( $( '#bannerModal' ).attr( 'data-itemId' ) );
        var locale  = $( this ).val()
        
        if ( itemId ) {
            $.ajax({
                type: 'GET',
                url: VsPath( 'vs_cms_banner_ext_edit', {'placeId': placeId, 'itemId': itemId, 'locale': locale} ),
                success: function ( response ) {
                    $( '#modalBodyBanner > div.card-body' ).html( response );
                }, 
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert( 'FATAL ERROR!!!' );
                }
            });
        }
    });
    
    $( '#btnSaveBanner' ).on( 'click', function( e )
    {
        if ( window.btnSaveBannerClicked ) {
            return;
        }
        window.btnSaveBannerClicked = true;
        
        var placeId    = $( '#FormContainer' ).attr( 'data-itemId' );
        var formData    = new FormData( $( '#FormBanner' )[ 0 ] );
        var submitUrl   = $( '#FormBanner' ).attr( 'action' );
        var redirectUrl = VsPath( 'vs_cms_banner_place_update', {'id': placeId} );
        
        VsFormSubmit( formData, submitUrl, redirectUrl );
    });
    
    VsRemoveDuplicates();
    initBannerImageField();
});
