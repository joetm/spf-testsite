var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    watch = require('gulp-watch'),
    //batch = require('gulp-batch'),
    sourcemaps = require('gulp-sourcemaps'),
    //sass = require('gulp-sass'),
    //less = require('gulp-less'),
    minifyCSS = require('gulp-minify-css'),
    //del = require('del'),
    rename = require('gulp-rename'),
    //gutil = require('gulp-util'),
    //jshint = require('gulp-jshint');
    //jslint = require('gulp-jslint');
    jslint = require('gulp-jslint-simple');


gulp.task('jslint', function () {
    gulp.src([
        'src/js/**/*.js' //,                 //include
        //'!js/**/node_modules/**/*.js' //exclude
    ])
    .pipe(jslint.run({
        node: true,
        nomen: true,
        vars: true,
        unparam: true,
        errorsOnly: false
    }))
    .pipe(jslint.report({
        reporter: require('jshint-stylish').reporter
    }));
});

gulp.task('processjs', function() {
    gulp.src(
        [
            'node_modules/spf/dist/spf.js',
            //'node_modules/spf/dist/boot.js',
            'src/js/*.js'
        ]
    )
    .pipe(uglify())
    .pipe(concat('scripts.min.js'))
    .pipe(rename({
        extname: '.min.js'
    }))
    .pipe(gulp.dest('dist/js/'));
});

//css
/*
gulp.task('scss', function () {
  gulp.src('frontend/css/*.scss')
    //.pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    //.pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('frontend/css'));
});
*/

//concatcss
gulp.task('concatcss', function() {
  return gulp.src([
        'src/css/style.css'
    ])
    .pipe(minifyCSS())
    .pipe(concat('styles.min.css'))
    .pipe(gulp.dest('dist/css/'));
});


/**********tasks***********/

gulp.task('css', ['concatcss']);
gulp.task('js', ['processjs']);

gulp.task('build', ['css', 'js']);

gulp.task('watch', ['js', 'css'], function () {
    //css
    gulp.watch([
        "./src/css/**/*.css",
        "./src/css/**/*.scss",
        "./src/css/**/*.less"
    ], ['css']);
    //js
    gulp.watch(["./src/js/**/*.js"], ['js']);
});//gulp.task('watch'
