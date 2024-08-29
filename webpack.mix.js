const mix = require('laravel-mix');

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

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .sass('resources/sass/app.scss', 'public/css')
    .version()
    .webpackConfig({
        cache: {
	  type: 'filesystem', // Usa la caché del sistema de archivos
	},
	    optimization: {
		        minimize: true, // Minifica los archivos para reducir el tamaño
		      }
	});
