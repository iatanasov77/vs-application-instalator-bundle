const Encore        = require( '@symfony/webpack-encore' );
const pathExists    = require( 'path-exists' );

const projectAssetsPath = './assets';
const baseAssetsPath    = './vendor/vankosoft/application/src/Vankosoft/ApplicationBundle/Resources/themes/default/assets';

Encore
    .setOutputPath( 'public/admin-panel/build/default/' )
    .setPublicPath( '/build/default/' )
  
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    
    .enableSassLoader(function(sassOptions) {}, {
        resolveUrlLoader: true
    })
    
    /**
     * Add Entries
     */
     .autoProvidejQuery()
     .configureFilenames({
        js: '[name].js?[contenthash]',
        css: '[name].css?[contenthash]',
        assets: '[name].[ext]?[hash:8]'
    })
    
    // FOS CkEditor
    .copyFiles([
        {from: './node_modules/ckeditor4/', to: 'ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
        
        // Add This When Debugging With Dev Package: https://github.com/ckeditor/ckeditor4.git
        // {from: './node_modules/ckeditor4/core', to: 'ckeditor/core/[path][name].[ext]'},
        
        {from: './node_modules/ckeditor4/adapters', to: 'ckeditor/adapters/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/lang', to: 'ckeditor/lang/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/skins', to: 'ckeditor/skins/[path][name].[ext]'}
    ])
    
    // CKeditor 4 Extra Plugins
    .copyFiles([
        {from: baseAssetsPath + '/vendor/ckeditor4_plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
    ])
    
    .copyFiles([
        {from: baseAssetsPath + '/images', to: 'images/[path][name].[ext]'},
        {from: './node_modules/bootstrap-sass/assets/fonts/bootstrap', to: 'fonts/bootstrap/[name].[ext]'},
    ])
     
    //////////////////////////////////////////////////////////////////
    // ASSETS
    //////////////////////////////////////////////////////////////////
    .addEntry( 'js/app', baseAssetsPath + '/js/app.js' )
    .addStyleEntry( 'css/global', baseAssetsPath + '/css/main.scss' )
    
    .addEntry( 'js/resource-delete', baseAssetsPath + '/js/pages/resource-delete.js' )
    
    .addEntry( 'js/settings', baseAssetsPath + '/js/pages/settings.js' )
    .addEntry( 'js/applications', baseAssetsPath + '/js/pages/applications.js' )
    .addEntry( 'js/profile', baseAssetsPath + '/js/pages/profile.js' )
    .addEntry( 'js/taxonomy-vocabolaries', baseAssetsPath + '/js/pages/taxonomy-vocabolaries.js' )
    .addEntry( 'js/taxonomy-vocabolaries-edit', baseAssetsPath + '/js/pages/taxonomy-vocabolaries-edit.js' )
    .addEntry( 'js/locales', baseAssetsPath + '/js/pages/locales.js' )
    .addEntry( 'js/cookie-consent-translations', baseAssetsPath + '/js/pages/cookie-consent-translations.js' )
    .addEntry( 'js/cookie-consent-translations-edit', baseAssetsPath + '/js/pages/cookie-consent-translations-edit.js' )
    .addEntry( 'js/tags-whitelist-contexts', baseAssetsPath + '/js/pages/tags-whitelist-contexts.js' )
    
    .addEntry( 'js/pages-categories', baseAssetsPath + '/js/pages/pages_categories.js' )
    .addEntry( 'js/pages-categories-edit', baseAssetsPath + '/js/pages/pages_categories_edit.js' )
    .addEntry( 'js/pages-index', baseAssetsPath + '/js/pages/pages-index.js' )
    .addEntry( 'js/pages-edit', baseAssetsPath + '/js/pages/pages-edit.js' )
    .addEntry( 'js/documents-index', baseAssetsPath + '/js/pages/documents-index.js' )
    .addEntry( 'js/documents-edit', baseAssetsPath + '/js/pages/documents-edit.js' )
    .addEntry( 'js/toc-pages', baseAssetsPath + '/js/pages/toc-pages.js' )
    .addEntry( 'js/toc-pages-delete', baseAssetsPath + '/js/pages/toc-pages-delete.js' )
    
    .addEntry( 'js/users-index', baseAssetsPath + '/js/pages/users-index.js' )
    .addEntry( 'js/users-edit', baseAssetsPath + '/js/pages/users-edit.js' )
    .addEntry( 'js/users-roles-index', baseAssetsPath + '/js/pages/users-roles-index.js' )
    .addEntry( 'js/users-roles-edit', baseAssetsPath + '/js/pages/users-roles-edit.js' )
    
    .addEntry( 'js/filemanager-index', baseAssetsPath + '/js/pages/filemanager-index.js' )
    .addEntry( 'js/filemanager-file-upload', baseAssetsPath + '/js/pages/filemanager-file-upload.js' )
    
    .addEntry( 'js/widget-groups', baseAssetsPath + '/js/pages/widget-groups.js' )
    .addEntry( 'js/widgets', baseAssetsPath + '/js/pages/widgets.js' )
    
    //////////////////////////////////////////////////////////////////
    // Payment Pages
    //////////////////////////////////////////////////////////////////
    .addEntry( 'js/gateway-config', baseAssetsPath + '/js/payment_pages/gateway-config.js' )
    .addEntry( 'js/currencies', baseAssetsPath + '/js/payment_pages/currencies.js' )
    .addEntry( 'js/exchange-rates', baseAssetsPath + '/js/payment_pages/exchange-rates.js' )
    .addEntry( 'js/product-categories', baseAssetsPath + '/js/payment_pages/product-categories.js' )
    .addEntry( 'js/product-categories-edit', baseAssetsPath + '/js/payment_pages/product-categories-edit.js' )
    .addEntry( 'js/products-index', baseAssetsPath + '/js/payment_pages/products-index.js' )
    .addEntry( 'js/products-edit', baseAssetsPath + '/js/payment_pages/products-edit.js' )
    .addEntry( 'js/pricing-plan-categories', baseAssetsPath + '/js/payment_pages/pricing-plan-categories.js' )
    .addEntry( 'js/pricing-plan-categories-edit', baseAssetsPath + '/js/payment_pages/pricing-plan-categories-edit.js' )
    .addEntry( 'js/pricing-plans-index', baseAssetsPath + '/js/payment_pages/pricing-plans-index.js' )
    .addEntry( 'js/pricing-plans-edit', baseAssetsPath + '/js/payment_pages/pricing-plans-edit.js' )
    .addEntry( 'js/recieved-payments', baseAssetsPath + '/js/payment_pages/recieved-payments.js' )
    .addEntry( 'js/orders', baseAssetsPath + '/js/payment_pages/orders.js' )
    .addEntry( 'js/pricing-plan-subscriptions', baseAssetsPath + '/js/payment_pages/pricing-plan-subscriptions.js' )
    .addEntry( 'js/pricing-plan-subscription-payments', baseAssetsPath + '/js/payment_pages/pricing-plan-subscription-payments.js' )
    .addEntry( 'js/stripe-webhook-endpoint', baseAssetsPath + '/js/payment_pages/stripe-webhook-endpoint.js' )
    .addEntry( 'js/coupon-objects', baseAssetsPath + '/js/payment_pages/coupon-objects.js' )
    .addEntry( 'js/coupons-index', baseAssetsPath + '/js/payment_pages/coupons-index.js' )
    .addEntry( 'js/coupons-edit', baseAssetsPath + '/js/payment_pages/coupons-edit.js' )
    
    //////////////////////////////////////////////////////////////////
    // Subscription Pages
    //////////////////////////////////////////////////////////////////
    .addEntry( 'js/payed-services-edit', baseAssetsPath + '/js/subscription_pages/payed-services-edit.js' )
    .addEntry( 'js/payed-services-listing', baseAssetsPath + '/js/subscription_pages/payed-services-listing.js' )
    .addEntry( 'js/payed-services-categories-listing', baseAssetsPath + '/js/subscription_pages/payed-services-categories-listing.js' )
    .addEntry( 'js/mailchimp-audiences-listing', baseAssetsPath + '/js/subscription_pages/mailchimp-audiences-listing.js' )
    .addEntry( 'js/payed-service-subscriptions', baseAssetsPath + '/js/subscription_pages/payed-service-subscriptions.js' )
;

if ( pathExists.sync( projectAssetsPath + '/test-js.js' ) ) {
    Encore
        .addEntry( 'test-js', baseAssetsPath + '/test-js.js' )
    ;
}

const config = Encore.getWebpackConfig();
config.name = 'adminPanel';

module.exports = config;
