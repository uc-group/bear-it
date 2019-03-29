var Encore = require('@symfony/webpack-encore');
const SWPrecacheWebpackPlugin = require('sw-precache-webpack-plugin');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/js/app.js')
    .disableSingleRuntimeChunk()
    .splitEntryChunks()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableStylusLoader()
    .enableVueLoader()
    .enableTypeScriptLoader()
    .addPlugin(new SWPrecacheWebpackPlugin({
        cacheId: 'bear-it-cache-id',
        dontCacheBustUrlsMatching: /\.\w{8}\./,
        filepath: 'public/sw.js',
        minify: Encore.isProduction(),
        staticFileGlobs: ['public/*.js', 'public/*.css', 'public/*.json'],
        navigateFallback: (process.env.PUBLIC_PATH || 'http://localhost'),
        staticFileGlobsIgnorePatterns: [/\.map$/, /manifest.json$/],
        dynamicUrlToDependencies: {
            '/': ['templates/homepage.html.twig']
        },
        mergeStaticsConfig: true,
        stripPrefix: 'public'
    }))
;

module.exports = Encore.getWebpackConfig();
