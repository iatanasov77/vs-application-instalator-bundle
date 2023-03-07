const Encore        = require('@symfony/webpack-encore');

const assetsPath    = './vendor/vankosoft/application/src/Vankosoft/ApplicationBundle/Resources/themes/default/assets';

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
        {from: assetsPath + '/vendor/ckeditor4_plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
    ])
    
    .copyFiles([
        {from: assetsPath + '/images', to: 'images/[path][name].[ext]'},
        {from: './node_modules/bootstrap-sass/assets/fonts/bootstrap', to: 'fonts/bootstrap/[name].[ext]'},
    ])
     
    //////////////////////////////////////////////////////////////////
    // ASSETS
    //////////////////////////////////////////////////////////////////
    .addEntry( 'js/app', assetsPath + '/js/app.js' )
    .addStyleEntry( 'css/global', assetsPath + '/css/main.scss' )
    
    .addEntry( 'js/settings', assetsPath + '/js/pages/settings.js' )
    .addEntry( 'js/applications', assetsPath + '/js/pages/applications.js' )
    .addEntry( 'js/profile', assetsPath + '/js/pages/profile.js' )
    .addEntry( 'js/taxonomy-vocabolaries', assetsPath + '/js/pages/taxonomy-vocabolaries.js' )
    .addEntry( 'js/taxonomy-vocabolaries-edit', assetsPath + '/js/pages/taxonomy-vocabolaries-edit.js' )
    
    .addEntry( 'js/pages-categories', assetsPath + '/js/pages/pages_categories.js' )
    .addEntry( 'js/pages-categories-edit', assetsPath + '/js/pages/pages_categories_edit.js' )
    .addEntry( 'js/pages-index', assetsPath + '/js/pages/pages-index.js' )
    .addEntry( 'js/pages-edit', assetsPath + '/js/pages/pages-edit.js' )
    .addEntry( 'js/documents-index', assetsPath + '/js/pages/documents-index.js' )
    .addEntry( 'js/documents-edit', assetsPath + '/js/pages/documents-edit.js' )
    .addEntry( 'js/toc-pages', assetsPath + '/js/pages/toc-pages.js' )
    .addEntry( 'js/toc-pages-delete', assetsPath + '/js/pages/toc-pages-delete.js' )
    
    .addEntry( 'js/users-index', assetsPath + '/js/pages/users-index.js' )
    .addEntry( 'js/users-edit', assetsPath + '/js/pages/users-edit.js' )
    .addEntry( 'js/users-roles-index', assetsPath + '/js/pages/users-roles-index.js' )
    .addEntry( 'js/users-roles-edit', assetsPath + '/js/pages/users-roles-edit.js' )
    
    .addEntry( 'js/filemanager-index', assetsPath + '/js/pages/filemanager-index.js' )
    .addEntry( 'js/filemanager-file-upload', assetsPath + '/js/pages/filemanager-file-upload.js' )
    
    //////////////////////////////////////////////////////////////////
    // Payment Pages
    //////////////////////////////////////////////////////////////////
    .addEntry( 'js/gateway-config', './assets/admin-panel/js/payment_pages/gateway-config.js' )
    
    //////////////////////////////////////////////////////////////////
    // Subscription Pages
    //////////////////////////////////////////////////////////////////
    .addEntry( 'js/payed-services-edit', './assets/admin-panel/js/subscription_pages/payed-services-edit.js' )
;

const config = Encore.getWebpackConfig();
config.name = 'adminPanel';

module.exports = config;
