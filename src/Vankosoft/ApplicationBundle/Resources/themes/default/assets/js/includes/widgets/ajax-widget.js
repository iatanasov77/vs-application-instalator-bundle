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
        },
 
        // The constructor
        _create: function() {
            var getSuggestionsUrl       = this.options.callback;
            var suggestionsContainer    = this.element;
            
            $.ajax({
                type: "GET",
                url: getSuggestionsUrl,
                success: function( response )
                {
                    suggestionsContainer.html( response );
                },
                error: function()
                {
                    alert( "AJAX WIDGET CALLBACK ERROR !!!" );
                }
            });
        },
    });
});