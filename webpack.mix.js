const mix = require('laravel-mix');

require('laravel-mix-auto-extract');

// this right here removes console log in production
mix.options({
    uglify: {
        uglifyOptions: {
            compress: {
                drop_console: true
            }
        }
    }
});

mix.webpackConfig({
    output: {
        publicPath: '/',
        filename: '[name].js?id=[hash]',
        chunkFilename: 'js/[name].[chunkhash].js'
    }
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// mix.js('resources/js/app.js', 'public/js')
//     .sass('resources/sass/app.scss', 'public/css');
mix.js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .version();

  if (mix.inProduction()) {
    mix.version();
    }
mix.autoExtract();
