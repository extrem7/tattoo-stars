const mix = require('laravel-mix')
const ChunkRenamePlugin = require('webpack-chunk-rename-plugin')
const config = require('./intelij.webpack.js')

mix.webpackConfig({
  output: {
    publicPath: 'admin/dist/',
    filename: '[name].js',
    chunkFilename: 'js/chunks/[name].js?id=[chunkhash]'
  },
  plugins: [
    new ChunkRenamePlugin({
      initialChunksWithEntry: true,
      '/admin/dist/js': '/admin/dist/js'
    }),
  ],
  devtool: 'source-map',
  ...config
})
  .setPublicPath('../../public/admin/dist/')

const options = {
  processCssUrls: false
}

mix.options(options)

mix.js(__dirname + '/resources/js/app.js', 'js/app.js')
  .vue()
  .extract()

mix.sass(__dirname + '/resources/scss/argon.scss', 'css/app.css')

mix.copyDirectory(__dirname + '/resources/assets/img', '../../public/admin/dist/img')
mix.copyDirectory(__dirname + '/resources/assets/vendor', '../../public/admin/dist/vendor')

mix.version(['../../public/admin/dist/js/app.js', '../../public/admin/dist/css/app.css'])

mix.sourceMaps()

mix.disableNotifications()

