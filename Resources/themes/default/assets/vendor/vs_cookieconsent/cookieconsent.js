/*
 * Manual: https://github.com/orestbida/cookieconsent/
 */
import "vanilla-cookieconsent/dist/cookieconsent.css";
import * as CookieConsent from "vanilla-cookieconsent";

/* */
const cookieconsentEn   = require( './translations/cookieconsent_en.js' );
const cookieconsentBg   = require( './translations/cookieconsent_bg.js' );

var testCookieconsentLanguages  = {
    ...cookieconsentEn,
    ...cookieconsentBg,
}
//console.log( JSON.stringify( cookieconsentLanguages, null, "\t" ) );


export function VsCookieConsent( cookieconsentLanguages, currentLang )
{
    CookieConsent.run({
        revision: 1,
        
        categories: {
            necessary: {
                enabled: true,  // this category is enabled by default
                readOnly: true  // this category cannot be disabled
            },
            analytics: {}
        },
    
        language: {
            default: currentLang,
            translations: cookieconsentLanguages
            //translations: testCookieconsentLanguages
        },
        
        guiOptions: {
            consentModal: {
                layout: 'cloud',
                position: 'bottom center'
            },
            preferencesModal: {
                layout: 'bar wide',
                position: 'left'
            }
        }
    });   
}


