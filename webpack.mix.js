let mix = require('laravel-mix');

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

mix
    /* CSS */
    .sass('resources/assets/sass/main.scss', 'html/css/dashmix.css')
    .sass('resources/assets/sass/dashmix/themes/xeco.scss', 'html/css/themes/')
    .sass('resources/assets/sass/dashmix/themes/xinspire.scss', 'html/css/themes/')
    .sass('resources/assets/sass/dashmix/themes/xmodern.scss', 'html/css/themes/')
    .sass('resources/assets/sass/dashmix/themes/xsmooth.scss', 'html/css/themes/')
    .sass('resources/assets/sass/dashmix/themes/xwork.scss', 'html/css/themes/')

    /* JS */
    .js('resources/assets/js/laravel/app.js', 'html/js/laravel.app.js')
    .js('resources/assets/js/dashmix/app.js', 'html/js/dashmix.app.js')

    /* Tools */
    .browserSync('localhost:8000')
    .disableNotifications()

    /* Options */
    .options({
        processCssUrls: false
    });
