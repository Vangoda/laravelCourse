module.exports = {
  devServer: {
    proxy: 'https://course.laravel'
  },

  // output built static files to Laravel's public dir.
  // note the "build" script in package.json needs to be modified as well.
  outputDir: '../../../public/',

  // modify the location of the generated HTML file.
  indexPath:
    process.env.NODE_ENV === 'production'
      ? '../../../resources/views/app.blade.php'
      : 'index.html'
}
