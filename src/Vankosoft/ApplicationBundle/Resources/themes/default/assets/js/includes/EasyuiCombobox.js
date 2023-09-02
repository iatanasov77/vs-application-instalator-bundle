/**
 * REQUIRED LIBS
 *==================
 * require( 'jquery-easyui/css/easyui.css' );
 * require( 'jquery-easyui/js/jquery.easyui.min.js' );
 *
 * @TODO: NEED IMPROVEMENTS AND REFACTORING
 */
export function EasyuiCombobox( selector, options )
{
    if ( ! Array.isArray( options.values ) ) {
        options.values  = [];    
    }
    
    selector.combobox({
        url: selector.attr( 'data-url' ),
        required: options.required,
        multiple: options.multiple,
        checkboxId: options.checkboxId,
        values: options.values,
        prompt: selector.attr( 'data-placeholder' ),
        
        valueField: 'id',
        textField: 'text',
        
        
        formatter: function( row )
        {
            var values  = $( this ).combobox( 'getData' );
            //console.log( values );
            //console.log( row );
            
            var opts = $( this ).combobox( 'options' );
            //alert( opts.checkboxId );
            
            return '<input type="checkbox" class="combobox-checkbox-' + opts.checkboxId + '" value="' + row[opts.valueField] + '">' + row[opts.textField];
        },
        
        onClick: function( node )
        {
            //console.log( node );
            //findNode( node.ID );
            
            var opts    = $( this ).combobox( 'options' );
            var el      = opts.finder.getEl( this, node[opts.valueField] );
            var checked = el.find( 'input.combobox-checkbox-' + opts.checkboxId )._propAttr( 'checked' );
            
            //var values    = $( this ).combobox( 'getValues' );
            //console.log( 'Selected Values: ' + values );
            
            if ( checked ) {
                //console.log( 'Checked: ' + checked );
                //$( this ).combobox( 'unselect', node[opts.valueField] );
            }
            
            //console.log( 'onClick 1' );
            setValues( opts, $( this ) );
            //console.log( 'onClick 2' );
        },
        
        onLoadSuccess: function()
        {
            var opts    = $( this ).combobox( 'options' );
            
            for ( let i = 0; i < opts.values.length; i++ ) {
                $( ".combobox-checkbox-" + opts.checkboxId + "[value=" + opts.values[i] + "]" ).prop( "checked", "true" );
            }
            
            setValues( opts, $( this ) );
        },
        
    /*
        onSelect: function( row )
        {
            console.log( row );
            var opts    = $( this ).combobox( 'options' );
            var el      = opts.finder.getEl( this, row[opts.valueField] );
            el.find( 'input.combobox-checkbox-' + opts.checkboxId )._propAttr( 'checked', true );
            
            //row['selected']   = true;
            
            //$( this ).combobox( 'setValues', [] );
            //$( this ).combobox( 'setValue', '' );
            
            var values  = $( this ).combobox( 'getValues' );
            console.log( 'Selected Values: ' + values );
        },
        
        onUnselect: function( row )
        {
            //console.log( row );
            var opts    = $( this ).combobox( 'options' );
            var el      = opts.finder.getEl( this, row[opts.valueField] );
            el.find( 'input.combobox-checkbox-' + opts.checkboxId )._propAttr( 'checked', false );
            
            //row['selected']   = false;
            //$( this ).combobox( 'clear' );
            
            //$( this ).combobox( 'setValues', [6,7] );
            //$( this ).combobox( 'setValue', '6,7' );
            //$( this ).combobox( 'loadData', [] );
        },
    */
        
        onChange: function( newValue, oldValue )
        {
            //console.log( 'onChange' );
            //console.log( newValue );
            
            var opts    = $( this ).combobox( 'options' );
            setValues( opts, $( this ) );
        }
    });
}

function setValues( opts, selector )
{
    let values  = [];
    $( 'input.combobox-checkbox-' + opts.checkboxId ).each( function( index )
    {
        if ( $( this ).is( ':checked' ) ) {
            //alert( "CHECKED: " + $( this ).val() );
            values.push( $( this ).val() );
        } else {
            //alert( "NOT CHECKED: " + $( this ).val() );
        }
    });
            
    selector.combobox( 'setValues', values );
}
