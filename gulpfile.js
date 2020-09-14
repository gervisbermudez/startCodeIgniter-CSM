const gulp = require("gulp");
const concat = require("gulp-concat");
const terser = require("gulp-terser");
const sass = require("gulp-sass");
const rename = require("gulp-rename");

const resources = "./resources/";
const public = "./public/";

gulp.task("concat_widgets", function () {
  return gulp
    .src([
      resources + "components/widget/*.js",
      resources + "components/dashboardModule.js",
    ])
    .pipe(concat("dashboardBundle.js"))
    .pipe(
      rename(function (path) {
        // Updates the object in-place
        path.dirname += "";
        path.basename += ".min";
        path.extname = ".js";
      })
    )
    .pipe(terser())
    .pipe(gulp.dest(public + "/js/components/"));
});

gulp.task("compress_js_components", function () {
  return gulp
    .src(resources + "components/*.js")
    .pipe(
      rename(function (path) {
        // Updates the object in-place
        path.dirname += "";
        path.basename += ".min";
        path.extname = ".js";
      })
    )
    .pipe(terser())
    .pipe(gulp.dest(public + "/js/components/"));
});

gulp.task("compress_js", function () {
  return gulp
    .src(resources + "js/*.js")
    .pipe(
      rename(function (path) {
        // Updates the object in-place
        path.dirname += "";
        path.basename += ".min";
        path.extname = ".js";
      })
    )
    .pipe(terser())
    .pipe(gulp.dest(public + "/js/"));
});

gulp.task("compile_sass", function () {
  return gulp
    .src(resources + "scss/admin/*.scss")
    .pipe(sass().on("error", sass.logError))
    .pipe(sass({ outputStyle: "compressed" }))
    .pipe(
      rename(function (path) {
        // Updates the object in-place
        path.dirname += "";
        path.basename += ".min";
        path.extname = ".css";
      })
    )
    .pipe(gulp.dest(public + "/css/admin/"));
});

gulp.task(
  "task_series",
  gulp.series("compress_js", "compress_js_components", "compile_sass")
);

gulp.task("watch_resources", function () {
  gulp.watch(
    [
      resources + "**/*.js",
      resources + "**/*.scss",
      "!" + resources + "components/*.scss",
    ],
    gulp.series("task_series")
  );
});

gulp.task("widget_task_series", gulp.series("concat_widgets", "compile_sass"));

gulp.task("watch_widget", function () {
  gulp.watch(
    [
      resources + "**/*.js",
      resources + "components/dashboardModule.js",
      resources + "**/*.scss",
      "!" + resources + "components/*.scss",
    ],
    gulp.series("widget_task_series")
  );
});

gulp.task("concat_form_components", function () {
  return gulp
    .src([
      resources + "components/formComponents/*.js",
      resources + "components/FormNewModule.js",
    ])
    .pipe(concat("FormNewModuleBundle.js"))
    .pipe(
      rename(function (path) {
        // Updates the object in-place
        path.dirname += "";
        path.basename += ".min";
        path.extname = ".js";
      })
    )
    .pipe(terser())
    .pipe(gulp.dest(public + "/js/components/"));
});

gulp.task("concat_form_content_components", function () {
  return gulp
    .src([
      resources + "components/formComponents/*.js",
      resources + "components/FormContentNewModule.js",
    ])
    .pipe(concat("FormContentNewModuleBundle.js"))
    .pipe(
      rename(function (path) {
        // Updates the object in-place
        path.dirname += "";
        path.basename += ".min";
        path.extname = ".js";
      })
    )
    .pipe(terser())
    .pipe(gulp.dest(public + "/js/components/"));
});

gulp.task(
  "form_task_series",
  gulp.series("concat_form_components", "concat_form_content_components")
);

gulp.task("watch_form", function () {
  gulp.watch(
    [
      resources + "components/FormNewModule.js",
      resources + "components/FormContentNewModule.js",
      resources + "**/*.scss",
      "!" + resources + "components/*.scss",
    ],
    gulp.series("form_task_series")
  );
});
