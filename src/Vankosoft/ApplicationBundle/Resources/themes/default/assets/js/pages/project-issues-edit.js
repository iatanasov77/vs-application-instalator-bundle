import Tagify from '@yaireo/tagify';
import '@yaireo/tagify/dist/tagify.css';

import DragSort from '@yaireo/dragsort';
import '@yaireo/dragsort/dist/dragsort.css';

var tagsInput;
var tagify;
var dragsort;

// must update Tagify's value according to the re-ordered nodes in the DOM
function onDragEnd( elm )
{
    tagify.updateValueByDOMTags();
}

$( function()
{
    var tagsInputWhitelist  = $( '#project_issue_form_labelsWhitelist' ).val().split( ',' );
    //console.log( tagsInputWhitelist );
    
    tagsInput   = $( '#project_issue_form_labels' )[0];
    tagify      = new Tagify( tagsInput, {
        whitelist : tagsInputWhitelist,
        dropdown : {
            classname     : "color-blue",
            enabled       : 0,              // show the dropdown immediately on focus
            maxItems      : 5,
            position      : "text",         // place the dropdown near the typed text
            closeOnSelect : false,          // keep the dropdown open after selecting a suggestion
            highlightFirst: true
        }
    });
    
    // bind "DragSort" to Tagify's main element and tell
    // it that all the items with the below "selector" are "draggable"
    dragsort    = new DragSort( tagify.DOM.scope, {
        selector: '.'+tagify.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEnd
        }
    });
});