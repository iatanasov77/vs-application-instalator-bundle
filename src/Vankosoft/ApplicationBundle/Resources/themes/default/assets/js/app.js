const $ = require( 'jquery' );
window.$ = $;

const bootstrap = require( 'bootstrap' );  // bootstrap should be before jquery-ui
window.bootstrap = bootstrap;

/* AdminPanel Layout */
require( '../vendor/slimscroll/jquery.slimscroll.js' );
require( './main.js' );
require( './menu.js' );
require( './authentication.js' );
