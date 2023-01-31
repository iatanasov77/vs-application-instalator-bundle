require( 'jquery-ui-dist/jquery-ui.js' );
require( 'jquery-ui-dist/jquery-ui.css' );
require( 'jquery-ui-dist/jquery-ui.theme.css' );
require( 'blueimp-file-upload/js/jquery.fileupload.js' );

import { humanFileSize } from './humanFileSize.js';

// WORKAROUND: Prevent Double Submiting
global.btnSaveUploadFileClicked = window.btnSaveUploadFileClicked = false;

export function InitOneUpFileUpload()
{
    ///////////////////////////////////////////////////////////////////////
    // https://github.com/blueimp/jQuery-File-Upload/wiki/Options
    ///////////////////////////////////////////////////////////////////////
    $( '#OneUpFileUpload' ).fileupload({
        url: '' + $( '#OneUpFileUpload' ).attr( 'data-endpoint' ),
        type: 'POST',
        dropZone: null,
        fileInput: $( '#upload_file_form_file' ),
        maxChunkSize: 1000000,
        autoUpload: false,
        add: function ( e, data )
        {
            $( '#btnSaveUploadFile' ).on( 'click', function ( e )
            {
                e.preventDefault();
                e.stopPropagation();
                
                if ( window.btnSaveUploadFileClicked ) {
                    return;
                }
                window.btnSaveUploadFileClicked   = true;
                
                $( this ).hide();
                data.submit();
            });
        },
        formData: function ( form )
        {
            //return form.serializeArray();
            
            /*
             * Send Values Needed For PostPersistListener In Backend
             *
             * If Files is Not Wrapped by Form Name Remove It From Here
             */
            return [
                {
                    name: 'formName',
                    value: $( '#formUpload' ).attr( 'name' )
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
    $( '#FileUploadProgressbar' ).progressbar({
        value: 0
    });
    
    $( '#OneUpFileUpload' ).on( 'fileuploadstart', function ( e, data )
    {
        $( '#FileUploadProgressbar' ).show();
    });
    
    $( '#OneUpFileUpload' ).on( 'fileuploadprogress', function ( e, data )
    {
        //console.log( data.loaded, data.total, data.bitrate );
        $( '#FileUploadProgressbar' ).progressbar({
            value: data.loaded,
            max: data.total
        });
        
        var progressPercents    = Math.round( ( data.loaded / data.total ) * 100 );
        var progressCaption     = humanFileSize( data.loaded, true ) + ' / ' + humanFileSize( data.total, true ) + ' ( ' + progressPercents + '% )';
        
        $( '#FileUploadProgressbar' ).children( 'span.caption' ).html( progressCaption );
    });
    
    // Uncomment Console Logs For Debugging
    $( '#OneUpFileUpload' ).on( 'fileuploaddone', function ( e, data )
    {
        e.preventDefault();
        e.stopPropagation();
        $( '#FileUploadProgressbar' ).hide();
        
        //console.log( 'FileUploadDone: ' );
        //console.log( data.result );
        
        document.location   = document.location;
    });
}
