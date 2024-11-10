require( 'jquery-ui-dist/jquery-ui.js' );
require( 'jquery-ui-dist/jquery-ui.css' );
require( 'jquery-ui-dist/jquery-ui.theme.css' );
require( 'blueimp-file-upload/js/jquery.fileupload.js' );

import { humanFileSize } from '../humanFileSize.js';

window.UploadedFiles                = [];
window.TestUploadProgressBarData    = {
    loaded: 0,
    total: 1000000000
};

/**
 * options
 * {
 *     fileuploadSelector: "#OneUpFileUpload",
 *     fileinputSelector: "#upload_file_form_file",
 *     btnStartUploadSelector: "#btnSaveUploadFile",
 *
 *     progressbarSelector: "#FileUploadProgressbar",
 *
 *     fileInputFieldName: "file",
 *     fileResourceId: 0,
 *     fileResourceKey: "",
 *     fileResourceClass: "",
 *     maxChunkSize: 10000000
 * }
 */
export function InitOneUpFileUpload( options, preFormSubmit = null )
{
    validateOptions( options );
    
    ///////////////////////////////////////////////////////////////////////
    // https://github.com/blueimp/jQuery-File-Upload/wiki/Options
    ///////////////////////////////////////////////////////////////////////
    $( options.fileuploadSelector ).fileupload({
        url: '' + $( options.fileuploadSelector  ).attr( 'data-endpoint' ),
        type: 'POST',
        dropZone: null,
        fileInput: $( options.fileinputSelector  ),
        maxChunkSize: options.maxChunkSize,
        autoUpload: false,
        add: function ( e, data )
        {
            $( options.btnStartUploadSelector ).on( 'click', function ( e )
            {
                e.preventDefault();
                //e.stopPropagation();
                
                $( this ).hide();
                
                let submitForm  = true;
                if ( preFormSubmit ) {
                     submitForm = preFormSubmit();
                }
                
                let fileName    = data.files[0].name;
                if ( ! window.UploadedFiles.includes( fileName ) && submitForm ) {
                    //console.log( fileName );
                    window.UploadedFiles.push( fileName );
                    
                    data.submit();
                }
            });
        },
        formData: function ( form )
        {
            //console.log( form );
            let formName    = form[0] ? form[0].name : '';
            
            /*
             * Send Values Needed For PostPersistListener In Backend
             *
             * If Files is Not Wrapped by Form Name Remove It From Here
             */
            return [
                {
                    name: 'formName',
                    value: formName
                },
                {
                    name: 'fileInputFieldName',
                    value: options.fileInputFieldName
                },
                {
                    name: 'fileResourceId',
                    value: options.fileResourceId
                },
                {
                    name: 'fileResourceClass',
                    value: options.fileResourceClass
                },
                {
                    name: 'fileResourceKey',
                    value: options.fileResourceKey
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
        
        let result  = JSON.parse( data.result );
        if ( ! ( "resourceKey" in result ) ) {
            return;
        }
        
        $( options.progressbarSelector ).hide();
        
        /*
        console.log( 'jQueryFileUpload Debuging:' );
        console.log( data );
        console.log( data.result );
        */
        
        window.dispatchEvent(
            new CustomEvent( "resourceUploaded", {
                detail: {
                    resourceKey: result.resourceKey,
                    resourceId: result.resourceId
                },
            })
        );
    });
}

/**
 * options
 * {
 *     btnStartUploadSelector: "#btnSaveUploadFile",
 *     progressbarSelector: "#FileUploadProgressbar"
 * }
 */
export function TestUploadProgressBar( options )
{
    $( options.btnStartUploadSelector ).on( 'click', function ( e )
    {
        e.preventDefault();
        e.stopPropagation();
        
        if ( window.TestUploadProgressBarStarted ) {
            return;
        }
        window.TestUploadProgressBarStarted = true;
        
        $( options.progressbarSelector ).progressbar({
            value: 0
        });
        
        $( options.progressbarSelector ).show();
        
        for( let i = 1; i < 100; i++ ) {
            TestUploadProgress( i, $( options.progressbarSelector ) );
        }
    });
}

function TestUploadProgress( delayIndex, selector )
{
    setTimeout(() => {
        window.TestUploadProgressBarData.loaded = window.TestUploadProgressBarData.total / ( 100 - delayIndex );
        
        selector.progressbar({
            value: window.TestUploadProgressBarData.loaded,
            max: window.TestUploadProgressBarData.total
        });
        
        var progressPercents    = Math.round( ( window.TestUploadProgressBarData.loaded / window.TestUploadProgressBarData.total ) * 100 );
        var progressCaption     = humanFileSize( window.TestUploadProgressBarData.loaded, true ) + ' / ' + humanFileSize( window.TestUploadProgressBarData.total, true ) + ' ( ' + progressPercents + '% )';
        
        selector.find( 'div.progressInfo > span.caption' ).html( progressCaption );
    }, delayIndex * 3000);
}

function validateOptions( options )
{
    let requiredKeys = [
        'fileuploadSelector',
        'fileinputSelector',
        'btnStartUploadSelector',
        'progressbarSelector',
        'fileInputFieldName',
        'fileResourceId',
        'fileResourceKey',
        'fileResourceClass'
    ];
    let checkAllKeys = requiredKeys.every( ( i ) => options.hasOwnProperty( i ) );
    
    if( ! checkAllKeys ) {
        throw new Error( 'Exception message' );
    }
}

function getFormFieldValue( form, field )
{
    if ( ! form[0] ) {
        return '';    
    }
    
    var formData = form.serializeArray();
    //console.log( formData );
    
    var myFieldName = form[0].name + '[' + field + ']';
    var myFieldFilter = function (field) {
        return field.name == myFieldName;
    }
    
    return formData.filter( myFieldFilter )[0].value;
}
