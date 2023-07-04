require( 'jquery-ui-dist/jquery-ui.js' );
require( 'jquery-ui-dist/jquery-ui.css' );
require( 'jquery-ui-dist/jquery-ui.theme.css' );
require( 'blueimp-file-upload/js/jquery.fileupload.js' );

import { humanFileSize } from './humanFileSize.js';

// WORKAROUND: Prevent Double Submiting
global.btnSaveUploadFileClicked = window.btnSaveUploadFileClicked = false;

/**
 * options
 * {
 *     fileuploadSelector: "#OneUpFileUpload",
 *     fileinputSelector: "#upload_file_form_file",
 *     btnStartUploadSelector: "#btnSaveUploadFile",
 *     isStartedHolder: "btnSaveUploadFileClicked",
 *
 *     progressbarSelector: "#FileUploadProgressbar"
 * }
 */
export function InitOneUpFileUpload( options )
{
    ///////////////////////////////////////////////////////////////////////
    // https://github.com/blueimp/jQuery-File-Upload/wiki/Options
    ///////////////////////////////////////////////////////////////////////
    $( options.fileuploadSelector ).fileupload({
        url: '' + $( options.fileuploadSelector  ).attr( 'data-endpoint' ),
        type: 'POST',
        dropZone: null,
        fileInput: $( options.fileinputSelector  ),
        maxChunkSize: 1000000,
        autoUpload: false,
        add: function ( e, data )
        {
            $( options.btnStartUploadSelector ).on( 'click', function ( e )
            {
                e.preventDefault();
                e.stopPropagation();
                
                if ( window[options.btnStartUploadSelector] ) {
                    return;
                }
                window[options.btnStartUploadSelector]   = true;
                
                $( this ).hide();
                data.submit();
            });
        },
        formData: function ( form )
        {
            //alert( form[0].name );
            
            var value   = getFormFieldValue( form, 'video_file_key' );
            alert( value );
            
            value   = getFormFieldValue( form, 'video_file_class' );
            alert( value );
            
            /*
             * Send Values Needed For PostPersistListener In Backend
             *
             * If Files is Not Wrapped by Form Name Remove It From Here
             */
            return [
                {
                    name: 'formName',
                    value: form[0].name
                },
                {
                    name: 'fileResourceId',
                    value: $( '#FileResourceId' ).val()
                },
                {
                    name: 'fileResourceClass',
                    value: $( '#FileResourceClass' ).val()
                },
                {
                    name: 'fileResourceOwner',
                    value: $( '#FileOwnerId' ).val()
                },
                {
                    name: 'fileOwnerClass',
                    value: $( '#FileOwnerClass' ).val()
                }
            ];
        }
    });
    
    /**
     * FileUpload Event Listeners
     * ===============
     * https://github.com/blueimp/jQuery-File-Upload/wiki/Options#callback-options
     */
    $( options.progressbarSelector ).progressbar({
        value: 0
    });
    
    $( options.fileuploadSelector ).on( 'fileuploadstart', function ( e, data )
    {
        $( options.progressbarSelector ).show();
    });
    
    $( options.fileuploadSelector ).on( 'fileuploadprogress', function ( e, data )
    {
        //console.log( data.loaded, data.total, data.bitrate );
        $( options.progressbarSelector ).progressbar({
            value: data.loaded,
            max: data.total
        });
        
        var progressPercents    = Math.round( ( data.loaded / data.total ) * 100 );
        var progressCaption     = humanFileSize( data.loaded, true ) + ' / ' + humanFileSize( data.total, true ) + ' ( ' + progressPercents + '% )';
        
        $( options.progressbarSelector ).find( 'div.progressInfo > span.caption' ).html( progressCaption );
    });
    
    // Uncomment Console Logs For Debugging
    $( options.fileuploadSelector ).on( 'fileuploaddone', function ( e, data )
    {
        e.preventDefault();
        e.stopPropagation();
        $( options.progressbarSelector ).hide();
        
        //console.log( 'FileUploadDone: ' );
        console.log( data );
        //console.log( data.result );
        
        document.location   = document.location;
    });
}

function getFormFieldValue( form, field )
{
    var formData = form.serializeArray();
    //console.log( formData );
    
    var myFieldName = form[0].name + '[' + field + ']';
    var myFieldFilter = function (field) {
        return field.name == myFieldName;
    }
    
    return formData.filter( myFieldFilter )[0].value;
}
