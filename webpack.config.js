var Encore = require('@symfony/webpack-encore');
const {InjectManifest} = require('workbox-webpack-plugin');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/js/app.js')
    .configureBabel(function (babelConfig) {
        babelConfig.presets[0][1].corejs = 2;
    })
    .disableSingleRuntimeChunk()
    .splitEntryChunks()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableStylusLoader()
    .enableVueLoader()
    .enableTypeScriptLoader()
    .addPlugin(new InjectManifest({
        swSrc: 'assets/js/sw.js',
        swDest: '../sw.js',
        dontCacheBustURLsMatching: /\.\w{8}\./
        // cacheId: 'bear-it-cache-id',
        // dontCacheBustUrlsMatching: /\.\w{8}\./,
        // filepath: 'public/sw.js',
        // minify: Encore.isProduction(),
        // //staticFileGlobs: [],
        // navigateFallback: (process.env.PUBLIC_PATH || 'http://localhost'),
        // staticFileGlobsIgnorePatterns: [/\.map$/, /manifest.json$/],
        // dynamicUrlToDependencies: {
        //     '/': ['templates/homepage.html.twig']
        // },
        // mergeStaticsConfig: true,
        // importScripts: [{chunkName: 'sw'}]
        // //stripPrefix: 'public'
    }))
;

module.exports = Encore.getWebpackConfig();
