const gulp = require('gulp');
const concat = require('gulp-concat');
const terser = require('gulp-terser');
const sass = require('gulp-sass');
const rename = require("gulp-rename");

const resources = './resources/';
const public = './public/';

/**
 * Compilar cotizador AP: cotizador-ap.min.js
 */
gulp.task('concat_widgets', function () {
    return gulp.src(
        [
            resources + 'components/widget/*.js',
            resources + 'components/dashboardModule.js',
        ])
        .pipe(concat('dashboardModule.js'))
        .pipe(gulp.dest(resources + 'components/'));
});

gulp.task('compress_js_components', function () {
    return gulp.src(resources + 'components/*.js')
        .pipe(rename(function (path) {
            // Updates the object in-place
            path.dirname += "";
            path.basename += ".min";
            path.extname = ".js";
        }))
        .pipe(terser())
        .pipe(gulp.dest(public + '/js/components/'));
}
);

gulp.task('compress_js', function () {
    return gulp.src(resources + 'js/*.js')
        .pipe(rename(function (path) {
            // Updates the object in-place
            path.dirname += "";
            path.basename += ".min";
            path.extname = ".js";
        }))
        .pipe(terser())
        .pipe(gulp.dest(public + '/js/'));
}
);

gulp.task('compile_sass', function () {
    return gulp.src(resources + 'scss/admin/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(sass({ outputStyle: 'compressed' }))
        .pipe(rename(function (path) {
            // Updates the object in-place
            path.dirname += "";
            path.basename += ".min";
            path.extname = ".css";
        }))
        .pipe(gulp.dest(public + '/css/admin/'));
});

gulp.task('task_series', gulp.series('compress_js', 'compress_js_components', 'compile_sass'));

gulp.task("watch_resources", function () {
    gulp.watch([resources + '**/*.js', resources + '**/*.scss', '!' + resources + 'components/*.scss'], gulp.series('task_series'));
});