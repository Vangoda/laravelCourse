const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  // local Laravel server address for api proxy
  devServer: { proxy: 'https://localhost:80' },

  // outputDir should be Laravel public dir
  outputDir: '../../../public/',

  // for production we use blade template file
  indexPath: process.env.NODE_ENV === 'production'
    ? '../resources/views/app.blade.php'
    : 'index.html',

  // Auto generated
  transpileDependencies: true,

  pluginOptions: {
    vuetify: {
			// https://github.com/vuetifyjs/vuetify-loader/tree/next/packages/vuetify-loader
		}
  }
})
