//* Vars
var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var sassGlob = require('gulp-sass-glob');

//* Tasks
gulp.task('floorplangrid', function () {
    return gulp.src('css/floorplangrid.scss')
        .pipe(sassGlob())
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('css/'))
});

gulp.task('single-properties', function () {
    return gulp.src('css/single-properties.scss')
        .pipe(sassGlob())
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('css/'))
});

gulp.task('floorplan-in-archive', function () {
    return gulp.src('css/floorplan-in-archive.scss')
        .pipe(sassGlob())
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('css/'))
});

gulp.task('search-properties-map', function () {
    return gulp.src('css/search-properties-map.scss')
        .pipe(sassGlob())
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('css/'))
});

gulp.task('properties-in-archive', function () {
    return gulp.src('css/properties-in-archive.scss')
        .pipe(sassGlob())
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('css/'))
});

//* Watchers here
gulp.task('watch', function () {
    gulp.watch('css/**/*.scss', gulp.series(['floorplangrid', 'single-properties', 'floorplan-in-archive', 'search-properties-map', 'properties-in-archive']));
})

gulp.task('default', gulp.series(['watch']));
