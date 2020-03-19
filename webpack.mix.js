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

//Auth Mixer
//mix.styles([
//    'public/css/bootstrap.min.css',
//    'public/js/toastr/toastr.min.css',
//    'public/css/style.css',
//    'public/css/buy.css',
//    'public/css/media.css',
//], 'public/css/auth-poundshop.css');
//
//mix.scripts([
//    'js/jquery-2.2.4.min.js',
//    'public/js/jQuery-validation-plugin/jquery.validate.js',
//    'public/js/jQuery-validation-plugin/additional-methods.js',
//    'public/js/propper.js',
//    'public/js/bootstrap-min.js',
//    'public/js/main.js',
//    'public/js/toastr/toastr.min.js',
//    'public/js/components/common.js'
//], 'public/js/auth-poundshop.js');

//Auth mixer

//Poundshop Admin mixer

mix.styles([
    'public/ss/animate.css',
    'public/css/perfect-scrollbar.css',
    'public/css/icon.css',
    'public/css/bootstrap.min.css',
    'public/css/bootstrap-datepicker.css',
    'public/js/toastr/toastr.min.css',
    'public/css/modal.css',
    'public/css/slick.css',
    'public/css/bootstrap-tagsinput.css',
    'public/css/media.css',
    'public/css/image-uploader.min.css',
    'public/css/bootstrap-tagsinput.css',
    'public/css/style.css',
    'public/css/developer.css',
], 'public/css/poundshop.css');

mix.scripts([
    'public/js/propper.js',
    'public/js/bootstrap-min.js',
    'public/js/bootstrap-datepicker.js',
    'public/js/bootstrap-tagsinput.min.js',
    'public/js/perfect-scrollbar.min.js',
    'public/js/toastr/toastr.min.js',
    'public/js/bootbox/bootbox.min.js',
    'public/js/jquery.responsivetabs.js',
    'public/js/slick.js',
    'public/js/custom-file-input.js',
    'public/js/main.js',
    'public/js/image-uploader.min.js',
], 'public/js/poundshop.js');