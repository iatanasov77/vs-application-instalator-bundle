require( 'jquery-ui-dist/jquery-ui.js' );
require( 'jquery-ui-dist/jquery-ui.css' );
require( 'jquery-ui-dist/jquery-ui.theme.css' );

$( function()
{
    /*
     * Using JqueryUi Widget Factory
     */
    $.widget( "custom.widget", {
        // default options
        options: {
            callback: 'default-callback-url',
            afterLoad: null,
        },
 
        // The constructor
        _create: function() {
            var getSuggestionsUrl       = this.options.callback;
            var suggestionsContainer    = this.element;
            var afterLoad               = this.options.afterLoad;
            
            $.ajax({
                type: "GET",
                url: getSuggestionsUrl,
                success: function( response )
                {
                    suggestionsContainer.html( response );
                    
                    if ( afterLoad ) {
                        afterLoad();
                    }
                },
                error: function()
                {
                    alert( "AJAX WIDGET CALLBACK ERROR !!!" );
                }
            });
        },
    });
});