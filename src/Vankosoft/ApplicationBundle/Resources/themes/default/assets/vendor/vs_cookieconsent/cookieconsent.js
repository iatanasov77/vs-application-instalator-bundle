/*
 * Manual: https://github.com/orestbida/cookieconsent/
 */
require( 'vanilla-cookieconsent/src/cookieconsent.js' );
require( 'vanilla-cookieconsent/src/cookieconsent.css' );

/*
const cookieconsentEn   = require( './translations/cookieconsent_en.js' );
const cookieconsentBg   = require( './translations/cookieconsent_bg.js' );

var cookieconsentLanguages  = {
    ...cookieconsentEn,
    ...cookieconsentBg,
}
//console.log( JSON.stringify( cookieconsentLanguages, null, "\t" ) );
*/

export function VsCookieConsent( cookieconsentLanguages, currentLang )
{
    var cookieconsent = initCookieConsent();
    
    cookieconsent.run({
        revision: 1,
        current_lang: currentLang,
        autoclear_cookies: true,    // default: false
        page_scripts: true,         // default: false
    
        languages: cookieconsentLanguages,
        
        gui_options: {
            consent_modal: {
                layout: 'cloud',               // box/cloud/bar
                position: 'bottom center',     // bottom/middle/top + left/right/center
                transition: 'slide',           // zoom/slide
                swap_buttons: false            // enable to invert buttons
            },
            settings_modal: {
                layout: 'box',                 // box/bar
                // position: 'left',           // left/right
                transition: 'slide'            // zoom/slide
            }
        }
    
    });    
}


