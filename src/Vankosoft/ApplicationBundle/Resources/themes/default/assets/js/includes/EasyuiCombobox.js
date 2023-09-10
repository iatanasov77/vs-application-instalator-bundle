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
    //selector.find( ':selected' ).removeAttr( 'selected' );
    
    selector.combobox({
        loadedBoxes: [],
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
            var opts            = $( this ).combobox( 'options' );
            var checkboxId      = 'combobox-checkbox-' + opts.checkboxId + '-' + row[opts.valueField];
            var checkboxClass   = 'combobox-checkbox-' + opts.checkboxId;
            
            return '<input type="checkbox" class="' + checkboxClass + '" value="' + row[opts.valueField] + '" id="' + checkboxId + '">' + row[opts.textField];
        },
        
        onLoadSuccess: function()
        {
            var opts    = $( this ).combobox( 'options' );
            
            if ( opts.loadedBoxes.includes( opts.checkboxId ) ) {
                //return;
            }
            //console.log( opts.values );
            opts.loadedBoxes.push( opts.checkboxId );
            
            if ( ! Array.isArray( opts.values ) ) {
                opts.values = [];
            }
            
            for ( let i = 0; i < opts.values.length; i++ ) {
                $( ".combobox-checkbox-" + opts.checkboxId + "[value=" + opts.values[i] + "]" ).prop( "checked", "true" );
                $( ".combobox-checkbox-" + opts.checkboxId + "[value=" + opts.values[i] + "]" ).attr( "checked", "checked" );
                
                //console.log( $( ".combobox-checkbox-" + opts.checkboxId + "[value=" + opts.values[i] + "]" ).val() );
                //console.log( $( ".combobox-checkbox-" + opts.checkboxId + "[value=" + opts.values[i] + "]" ).prop( "checked" ) );
            }
            
            // https://stackoverflow.com/questions/13943511/remove-data-from-jquery-easyui-combobox
            $( '.combobox-checkbox-' + opts.checkboxId ).parent(  '.combobox-item-selected'  ).remove();

            setValues( opts, $( this ) );
        },
        
        onClick: function( node )
        {
            var opts    = $( this ).combobox( 'options' );
            var el      = opts.finder.getEl( this, node[opts.valueField] );
            var checked = el.find( 'input.combobox-checkbox-' + opts.checkboxId )._propAttr( 'checked' );
            
            if ( checked ) {
                $( this ).combobox( 'select', node[opts.valueField] );
                $( this ).combobox( 'select', node[opts.textField] );
            } else {
                $( this ).combobox( 'unselect', node[opts.valueField] );
                $( this ).combobox( 'unselect', node[opts.textField] );
            }
            
            setValues( opts, $( this ) );
        },
        
        onChange: function( newValue, oldValue )
        {
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
            values.push( $( this ).val() );
        }
    });
    
    //selector.combobox( 'setValues', values );
    
    let uniqueValues = [...new Set( values )];
    selector.combobox( 'setValues', uniqueValues );
}
