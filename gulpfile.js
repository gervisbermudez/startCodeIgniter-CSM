const gulp = require('gulp');
const concat = require('gulp-concat');
const terser = require('gulp-terser');
const sass = require('gulp-sass');
const rename = require("gulp-rename");

const resources = './resources/';
const public = './public/';

gulp.task('compress_js', function () {
    return gulp.src(resources + 'components/*.js')
        .pipe(rename(function (path) {
            // Updates the object in-place
            path.dirname += "";
            path.basename += ".min";
            path.extname = ".js";
        }))
        .pipe(terser())
        .pipe(gulp.dest(public + '/components/js'));
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

gulp.task('task_series', gulp.series('compress_js', 'compile_sass'));

gulp.task("watch_resources", function () {
    gulp.watch([resources + '**/*.js', resources + '**/*.scss', '!' + resources + 'components/*.scss'], gulp.series('task_series'));
});