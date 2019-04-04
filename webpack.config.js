var Encore = require('@symfony/webpack-encore')
var path = require('path')
const { InjectManifest } = require('workbox-webpack-plugin')

Encore.setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './assets/js/app.ts')
  .configureBabel(function(babelConfig) {
    babelConfig.presets[0][1].corejs = 2
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
  .addPlugin(
    new InjectManifest({
      swSrc: 'assets/js/sw.js',
      swDest: '../sw.js',
      dontCacheBustURLsMatching: /\.\w{8}\./
    })
  )
  .addAliases({
    '~': path.resolve(__dirname, 'assets/js'),
    '@pages': path.resolve(__dirname, 'assets/js/pages')
  })

module.exports = Encore.getWebpackConfig()
