// Manual: https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/blob/master/Resources/doc/usage.rst
// bin/console fos:js-routing:dump --format=json --target=public/shared_assets/js/fos_js_routes_admin.json
///////////////////////////////////////////////////////////////////////////////////////////////////////////
import Routing from '../../../../../../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

export function VsPath( route, params, routes )
{
    if ( ! routes ) {
        var routes  = require( '../../../../../../../../../../../../public/shared_assets/js/fos_js_routes_admin.json' );
        Routing.setRoutingData( routes );
    } else {
        Routing.setRoutingData( routes );
    }
    
    return Routing.generate( route, params )
}
