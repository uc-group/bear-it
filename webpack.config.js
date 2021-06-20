var Encore = require('@symfony/webpack-encore')
var path = require('path')
const { InjectManifest } = require('workbox-webpack-plugin')

Encore.setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './assets/js/app.js')
  .configureBabel(function(babelConfig) {
    babelConfig.presets[0][1].corejs = 2
  })
  .disableSingleRuntimeChunk()
  .splitEntryChunks()
  .cleanupOutputBeforeBuild()
  .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .enableSassLoader(function (options) {
      options.implementation = require('sass')
      options.sassOptions = {
          fiber: require('fibers')
      }
  })
  .enableVueLoader()
  // .addPlugin(
  //   new InjectManifest({
  //     swSrc: './assets/js/sw.js',
  //     swDest: '../sw.js',
  //     dontCacheBustURLsMatching: /\.\w{8}\./
  //   })
  // )
  .addAliases({
    '~': path.resolve(__dirname, 'assets/js'),
    '@pages': path.resolve(__dirname, 'assets/js/pages'),
    '@api': path.resolve(__dirname, 'assets/js/api'),
    '@lib': path.resolve(__dirname, 'assets/js/lib'),
    '@publiclib': path.resolve(__dirname, 'public/js/lib'),
  })

module.exports = Encore.getWebpackConfig()
