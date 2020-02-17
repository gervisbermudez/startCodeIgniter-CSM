var gulp = require('gulp');
//Concatenar JS
var concat = require('gulp-concat');
//Minificar Js 
const terser = require('gulp-terser');
var sass = require('gulp-sass');

const resources = './resources/';
const public = './public/';

gulp.task('compress_js', function () {
    return gulp.src(resources + 'components/*.js')
        .pipe(terser())
        .pipe(gulp.dest(public + '/js'));
}
);

gulp.task('compile_sass', function () {
    return gulp.src(resources + 'scss/admin/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest(public + '/css/admin/'));
});

gulp.task('task_series', gulp.series('compress_js', 'compile_sass'));

gulp.task("watch_resources", function () {
    gulp.watch([resources + '**/*.js', resources + '**/*.scss', '!' + resources + 'components/*.scss'], gulp.series('task_series'));
});