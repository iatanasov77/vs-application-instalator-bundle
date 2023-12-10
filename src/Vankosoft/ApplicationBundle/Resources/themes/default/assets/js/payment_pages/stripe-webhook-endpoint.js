require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );

import { EasyuiCombobox } from 'jquery-easyui-extensions/EasyuiCombobox.js';

$( function ()
{
    let selectedEvents  = JSON.parse( $( '#webhook_endpoint_form_enabled_events').val() );
    EasyuiCombobox( $( '#webhook_endpoint_form_enabled_events' ), {
        required: false,
        multiple: true,
        checkboxId: "webhook_endpoint_events",
        values: selectedEvents
    });
});