var gulp = require('gulp');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');

var less = require('gulp-less');
var minifyCSS = require('gulp-minify-css');

var DEST = 'libs/easySDI_leaflet.pack';

gulp.task('default', function () {

    gulp.src([
        'libs/i18next-1.9.0/i18next-1.9.0.js',
        'libs/shramov/tile/Google.js',
        'libs/shramov/tile/Bing.js',
        'libs/leaflet.TileLayer.WMTS-master/leaflet-tilelayer-wmts-src.js',
        'libs/Leaflet.ZoomBox/L.Control.ZoomBox.js',
        'libs/leaflet-measure/leaflet-measure.js',
        'libs/leaflet-control-geocoder/Control.Geocoder.js',
        'libs/leaflet-EasyPrint/L.Control.EasyPrint.js',
        'libs/wms-capabilities/wms-capabilities.min.js',
        'libs/sidebar-v2/js/leaflet-sidebar.js',
        'libs/leaflet-EasyLayer/easyLayer.js',
        'libs/leaflet-EasyAddLayer/easyAddLayer.js',
        'libs/leaflet-EasyLegend/easyLegend.js',
        'libs/leaflet-EasyGetFeature/easyGetFeature.js',
        'libs/leaflet-graphicscale/Leaflet.GraphicScale.min.js',
        'libs/easysdi_leaflet/easysdi_leaflet.js'
    ], {
        base: 'libs'
    })
    // .pipe(sourcemaps.init())
    .pipe(concat('easySDI_leaflet.pack.js'))
    //  .pipe(sourcemaps.write())
    .pipe(gulp.dest(DEST))
        .pipe(uglify())
        .pipe(rename({
            extname: '.min.js'
        }))
        .pipe(gulp.dest(DEST));


    gulp.src('libs/main.less')
        .pipe(less())
        .pipe(minifyCSS())
        .pipe(gulp.dest(DEST));
});