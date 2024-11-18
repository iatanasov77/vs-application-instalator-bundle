const Encore        = require( '@symfony/webpack-encore' );
const path          = require( 'path' );
const pathExists    = require( 'path-exists' );

const projectAssetsPath             = './assets';
const applicationAssetsPath         = './vendor/vankosoft/application/src/Vankosoft/ApplicationBundle/Resources/themes/default/assets';
const usersSubscriptionsAssetsPath  = './vendor/vankosoft/users-subscriptions-bundle/lib/Resources/themes/default/assets';
const paymentAssetsPath             = './vendor/vankosoft/payment-bundle/lib/Resources/themes/default/assets';
const catalogAssetsPath             = './vendor/vankosoft/catalog-bundle/lib/Resources/themes/default/assets';

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
    
    .addAliases({
        '@': path.resolve( __dirname, 'assets' )
    })
    
    // Default Theme Images
    .copyFiles({
         from: applicationAssetsPath + '/images',
         to: 'images/[path][name].[ext]',
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
        {from: applicationAssetsPath + '/vendor/ckeditor4_plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
    ])
     
    //////////////////////////////////////////////////////////////////
    // ASSETS
    //////////////////////////////////////////////////////////////////
    .addEntry( 'js/app', applicationAssetsPath + '/js/app.js' )
    .addStyleEntry( 'css/global', applicationAssetsPath + '/css/main.scss' )
    
    .addEntry( 'js/resource-delete', applicationAssetsPath + '/js/pages/resource-delete.js' )
    
    .addEntry( 'js/settings', applicationAssetsPath + '/js/pages/settings.js' )
    .addEntry( 'js/applications', applicationAssetsPath + '/js/pages/applications.js' )
    .addEntry( 'js/profile', applicationAssetsPath + '/js/pages/profile.js' )
    .addEntry( 'js/taxonomy-vocabolaries', applicationAssetsPath + '/js/pages/taxonomy-vocabolaries.js' )
    .addEntry( 'js/taxonomy-vocabolaries-edit', applicationAssetsPath + '/js/pages/taxonomy-vocabolaries-edit.js' )
    .addEntry( 'js/locales', applicationAssetsPath + '/js/pages/locales.js' )
    .addEntry( 'js/cookie-consent-translations', applicationAssetsPath + '/js/pages/cookie-consent-translations.js' )
    .addEntry( 'js/cookie-consent-translations-edit', applicationAssetsPath + '/js/pages/cookie-consent-translations-edit.js' )
    .addEntry( 'js/tags-whitelist-contexts', applicationAssetsPath + '/js/pages/tags-whitelist-contexts.js' )
    .addEntry( 'js/tags-whitelist-contexts-edit', applicationAssetsPath + '/js/pages/tags-whitelist-contexts-edit.js' )
    
    .addEntry( 'js/pages-categories', applicationAssetsPath + '/js/pages/pages_categories.js' )
    .addEntry( 'js/pages-categories-edit', applicationAssetsPath + '/js/pages/pages_categories_edit.js' )
    .addEntry( 'js/pages-index', applicationAssetsPath + '/js/pages/pages-index.js' )
    .addEntry( 'js/pages-edit', applicationAssetsPath + '/js/pages/pages-edit.js' )
    .addEntry( 'js/documents-index', applicationAssetsPath + '/js/pages/documents-index.js' )
    .addEntry( 'js/documents-edit', applicationAssetsPath + '/js/pages/documents-edit.js' )
    .addEntry( 'js/toc-pages', applicationAssetsPath + '/js/pages/toc-pages.js' )
    .addEntry( 'js/toc-pages-delete', applicationAssetsPath + '/js/pages/toc-pages-delete.js' )
    .addEntry( 'js/multipage-toc-update', applicationAssetsPath + '/js/pages/multipage-toc-update.js' )
    
    .addEntry( 'js/users-index', applicationAssetsPath + '/js/pages/users-index.js' )
    .addEntry( 'js/users-edit', applicationAssetsPath + '/js/pages/users-edit.js' )
    .addEntry( 'js/users-roles-index', applicationAssetsPath + '/js/pages/users-roles-index.js' )
    .addEntry( 'js/users-roles-edit', applicationAssetsPath + '/js/pages/users-roles-edit.js' )
    
    .addEntry( 'js/filemanager-index', applicationAssetsPath + '/js/pages/filemanager-index.js' )
    .addEntry( 'js/filemanager-file-upload', applicationAssetsPath + '/js/pages/filemanager-file-upload.js' )
    
    .addEntry( 'js/widget-groups', applicationAssetsPath + '/js/pages/widget-groups.js' )
    .addEntry( 'js/widgets', applicationAssetsPath + '/js/pages/widgets.js' )
    .addEntry( 'js/widgets-edit', applicationAssetsPath + '/js/pages/widgets-edit.js' )
    
    .addEntry( 'js/helpcenter-questions', applicationAssetsPath + '/js/pages/helpcenter-questions.js' )
    .addEntry( 'js/helpcenter-questions-edit', applicationAssetsPath + '/js/pages/helpcenter-questions-edit.js' )
    .addEntry( 'js/quick-links', applicationAssetsPath + '/js/pages/quick-links.js' )
    .addEntry( 'js/quick-links-edit', applicationAssetsPath + '/js/pages/quick-links-edit.js' )
    .addEntry( 'js/sliders', applicationAssetsPath + '/js/pages/sliders.js' )
    .addEntry( 'js/sliders-edit', applicationAssetsPath + '/js/pages/sliders-edit.js' )
    .addEntry( 'js/sliders-items', applicationAssetsPath + '/js/pages/sliders-items.js' )
    .addEntry( 'js/sliders-items-edit', applicationAssetsPath + '/js/pages/sliders-items-edit.js' )
    
    .addEntry( 'js/banner-places', applicationAssetsPath + '/js/pages/banner-places.js' )
    .addEntry( 'js/banner-places-edit', applicationAssetsPath + '/js/pages/banner-places-edit.js' )
    .addEntry( 'js/banners', applicationAssetsPath + '/js/pages/banners.js' )
    .addEntry( 'js/banners-edit', applicationAssetsPath + '/js/pages/banners-edit.js' )
    .addEntry( 'js/banner-modal', applicationAssetsPath + '/js/pages/banner-modal.js' )
    
    .addEntry( 'js/project-issues', applicationAssetsPath + '/js/pages/project-issues.js' )
    .addEntry( 'js/project-issues-edit', applicationAssetsPath + '/js/pages/project-issues-edit.js' )
;

//////////////////////////////////////////////////////////////////
// Subscription Pages
//////////////////////////////////////////////////////////////////
if ( pathExists.sync( usersSubscriptionsAssetsPath ) ) {
    Encore
        .addEntry( 'js/payed-services-edit', usersSubscriptionsAssetsPath + '/js/pages/payed-services-edit.js' )
        .addEntry( 'js/payed-services-listing', usersSubscriptionsAssetsPath + '/js/pages/payed-services-listing.js' )
        .addEntry( 'js/mailchimp-audiences-listing', usersSubscriptionsAssetsPath + '/js/pages/mailchimp-audiences-listing.js' )
        .addEntry( 'js/payed-service-subscriptions', usersSubscriptionsAssetsPath + '/js/pages/payed-service-subscriptions.js' )
    ;
}

//////////////////////////////////////////////////////////////////
// Payment Pages
//////////////////////////////////////////////////////////////////
if ( pathExists.sync( paymentAssetsPath ) ) {
    Encore
        .addEntry( 'js/gateway-config', paymentAssetsPath + '/js/pages/gateway-config.js' )
        .addEntry( 'js/currencies', paymentAssetsPath + '/js/pages/currencies.js' )
        .addEntry( 'js/exchange-rates', paymentAssetsPath + '/js/pages/exchange-rates.js' )
        .addEntry( 'js/recieved-payments', paymentAssetsPath + '/js/pages/recieved-payments.js' )
        .addEntry( 'js/orders', paymentAssetsPath + '/js/pages/orders.js' )
        .addEntry( 'js/stripe-webhook-endpoint', paymentAssetsPath + '/js/pages/stripe-webhook-endpoint.js' )
        .addEntry( 'js/coupon-objects', paymentAssetsPath + '/js/pages/coupon-objects.js' )
        .addEntry( 'js/coupons-index', paymentAssetsPath + '/js/pages/coupons-index.js' )
        .addEntry( 'js/coupons-edit', paymentAssetsPath + '/js/pages/coupons-edit.js' )
        .addEntry( 'js/promotions-index', paymentAssetsPath + '/js/pages/promotions-index.js' )
        .addEntry( 'js/promotions-edit', paymentAssetsPath + '/js/pages/promotions-edit.js' )
        .addEntry( 'js/promotion-coupons-index', paymentAssetsPath + '/js/pages/promotion-coupons-index.js' )
        .addEntry( 'js/promotion-coupons-edit', paymentAssetsPath + '/js/pages/promotion-coupons-edit.js' )
        .addEntry( 'js/customer-groups', paymentAssetsPath + '/js/pages/customer-groups.js' )
        .addEntry( 'js/customer-groups-edit', paymentAssetsPath + '/js/pages/customer-groups-edit.js' )
        .addEntry( 'js/stripe-objects', paymentAssetsPath + '/js/pages/stripe-objects.js' )
    ;
}
    
//////////////////////////////////////////////////////////////////
// Catalog Pages
//////////////////////////////////////////////////////////////////
if ( pathExists.sync( catalogAssetsPath ) ) {
    Encore
        .addEntry( 'js/product-categories', catalogAssetsPath + '/js/pages/product-categories.js' )
        .addEntry( 'js/product-categories-edit', catalogAssetsPath + '/js/pages/product-categories-edit.js' )
        .addEntry( 'js/products-index', catalogAssetsPath + '/js/pages/products-index.js' )
        .addEntry( 'js/products-edit', catalogAssetsPath + '/js/pages/products-edit.js' )
        .addEntry( 'js/pricing-plan-categories', catalogAssetsPath + '/js/pages/pricing-plan-categories.js' )
        .addEntry( 'js/pricing-plan-categories-edit', catalogAssetsPath + '/js/pages/pricing-plan-categories-edit.js' )
        .addEntry( 'js/pricing-plans-index', catalogAssetsPath + '/js/pages/pricing-plans-index.js' )
        .addEntry( 'js/pricing-plans-edit', catalogAssetsPath + '/js/pages/pricing-plans-edit.js' )
        .addEntry( 'js/pricing-plan-subscriptions', catalogAssetsPath + '/js/pages/pricing-plan-subscriptions.js' )
        .addEntry( 'js/pricing-plan-subscription-payments', catalogAssetsPath + '/js/pages/pricing-plan-subscription-payments.js' )
        .addEntry( 'js/association-types-index', catalogAssetsPath + '/js/pages/association-types-index.js' )
    ;
}

const config = Encore.getWebpackConfig();
config.name = 'adminPanel';

module.exports = config;
