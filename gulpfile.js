'use strict';

const gulp = require('gulp');
const sass = require('gulp-sass');
const terser = require('gulp-terser');
const concat = require('gulp-concat');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const uglify = require('gulp-uglify');
const minify = require("gulp-minify");

sass.compiler = require('node-sass');

function css() {
    return gulp.src('./assets/scss/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(sourcemaps.write())
        .pipe(concat('style.min.css'))
        .pipe(gulp.dest('./assets/css'));
}

function js() {
    return gulp.src(['./assets/js/src/*.js'])
        .pipe(concat('main.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./assets/js'));
}

gulp.task('watch', function () {
    gulp.watch('./assets/js/src/*.js', js);
    gulp.watch('./assets/scss/**/*.scss', css);
});
