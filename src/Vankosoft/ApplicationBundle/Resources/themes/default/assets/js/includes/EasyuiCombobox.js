/**
 * REQUIRED LIBS
 *==================
 * require( 'jquery-easyui/css/easyui.css' );
 * require( 'jquery-easyui/js/jquery.easyui.min.js' );
 *
 * @TODO: NEED IMPROVEMENTS
 */
export function EasyuiCombobox( selector, options )
{
    selector.combobox({
        url: selector.attr( 'data-url' ),
        required: options.required,
        multiple: options.multiple,
        checkbox: options.checkbox,
        prompt: selector.attr( 'data-placeholder' ),
        
        valueField: 'id',
        textField: 'text',
    
        onClick: function( node )
        {
           //console.log( node );
           //findNode( node.ID );
           clickNode( selector, node.id );
        },
        
        formatter: function( row )
        {
            var opts = $( this ).combobox( 'options' );
            return '<input type="checkbox" class="combobox-checkbox">' + row[opts.textField]
        },
        
        onLoadSuccess: function()
        {
            var opts    = $( this ).combobox( 'options' );
            var target  = this;
            var values  = $( target ).combobox( 'getValues' );
            $.map( values, function( value )
            {
                var el  = opts.finder.getEl( target, value );
                el.find( 'input.combobox-checkbox' )._propAttr( 'checked', true );
            })
        },
        
        onSelect: function( row )
        {
            //console.log( row )
            var opts    = $( this ).combobox( 'options' );
            var el      = opts.finder.getEl( this, row[opts.valueField] );
            el.find( 'input.combobox-checkbox' )._propAttr( 'checked', true );
        },
        
        onUnselect: function( row )
        {
            var opts    = $( this ).combobox( 'options' );
            var el      = opts.finder.getEl( this, row[opts.valueField] );
            el.find( 'input.combobox-checkbox' )._propAttr( 'checked', false );
        }
    });
}

function clickNode( cc, id )
{
    var opts        = cc.combobox( 'options' );
    
    /*
    var el          = opts.finder.getEl( this, row[opts.valueField] );
    var isChecked   = el.find( 'input.combobox-checkbox' )._propAttr( 'checked' );  
    console.log( isChecked );
    
    
    if ( node.checked ){
        el.find( 'input.combobox-checkbox' )._propAttr( 'checked', false );
    } else {
        el.find( 'input.combobox-checkbox' )._propAttr( 'checked', true );
    }
    */
}
