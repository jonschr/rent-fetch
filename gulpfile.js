//* Vars
var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var sassGlob = require('gulp-sass-glob');
const cleanCSS = require('gulp-clean-css');

//* Tasks for dev
gulp.task('floorplangrid', function () {
    return gulp
        .src('css/floorplangrid.scss')
        .pipe(sassGlob())
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('css/'));
});

gulp.task('single-properties', function () {
    return gulp
        .src('css/single-properties.scss')
        .pipe(sassGlob())
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('css/'));
});

gulp.task('floorplan-in-archive', function () {
    return gulp
        .src('css/floorplan-in-archive.scss')
        .pipe(sassGlob())
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('css/'));
});

gulp.task('search-properties-map', function () {
    return gulp
        .src('css/search-properties-map.scss')
        .pipe(sassGlob())
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('css/'));
});

gulp.task('properties-in-archive', function () {
    return gulp
        .src('css/properties-in-archive.scss')
        .pipe(sassGlob())
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('css/'));
});

//* Tasks for prod
gulp.task('floorplangrid-prod', function () {
    return gulp
        .src('css/floorplangrid.scss')
        .pipe(sassGlob())
        .pipe(sass().on('error', sass.logError))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('css/'));
});

gulp.task('single-properties-prod', function () {
    return gulp
        .src('css/single-properties.scss')
        .pipe(sassGlob())
        .pipe(sass().on('error', sass.logError))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('css/'));
});

gulp.task('floorplan-in-archive-prod', function () {
    return gulp
        .src('css/floorplan-in-archive.scss')
        .pipe(sassGlob())
        .pipe(sass().on('error', sass.logError))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('css/'));
});

gulp.task('search-properties-map-prod', function () {
    return gulp
        .src('css/search-properties-map.scss')
        .pipe(sassGlob())
        .pipe(sass().on('error', sass.logError))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('css/'));
});

gulp.task('properties-in-archive-prod', function () {
    return gulp
        .src('css/properties-in-archive.scss')
        .pipe(sassGlob())
        .pipe(sass().on('error', sass.logError))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('css/'));
});

gulp.task('admin', function () {
    return gulp
        .src('css/admin.scss')
        .pipe(sassGlob())
        .pipe(sass().on('error', sass.logError))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('css/'));
});

//* Watchers here
gulp.task('watch', function () {
    gulp.watch(
        'css/**/*.scss',
        gulp.series([
            'floorplangrid',
            'single-properties',
            'floorplan-in-archive',
            'search-properties-map',
            'properties-in-archive',
            'admin',
        ])
    );
});

gulp.task('prod', function () {
    gulp.watch(
        'css/**/*.scss',
        gulp.series([
            'floorplangrid-prod',
            'single-properties-prod',
            'floorplan-in-archive-prod',
            'search-properties-map-prod',
            'properties-in-archive-prod',
            'admin',
        ])
    );
});

gulp.task('default', gulp.series(['watch']));
