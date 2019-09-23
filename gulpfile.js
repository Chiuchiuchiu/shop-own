var gulp = require('gulp'),
    ignore = require('gulp-ignore'),
    gutil = require('gulp-util'),
    cached = require('gulp-cached'),
    sass = require('gulp-sass'),
    rev = require('gulp-rev-append'),
    imageMin = require('gulp-imagemin'),
    minifyCss = require('gulp-minify-css'),
    autoprefixer = require('gulp-autoprefixer'),
    clean = require('gulp-clean'),
    gulpSequence = require('gulp-sequence'),
    base64 = require('gulp-base64'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    ts = require("gulp-typescript"),
    order = require("gulp-order"),
    // sourcemaps = require('gulp-sourcemaps'),
    changed = require("gulp-changed"),
    minimist = require('minimist');

var options = minimist(process.argv.slice(2), {
    string: 'p',
    default: { p: process.env.NODE_ENV || 'www' }
});

var config = require('./gulp/'+options.p+'-config.json');

var isBuild = false;
gulp.task('default', function (callback) {

});

gulp.task('ts', function (cb) {
    var outPath = isBuild ? config.buildPath : config.distPath;
    return gulp.src(config.ts.src)
        .pipe(isBuild?gutil.noop():changed(outPath+config.ts.path,{extension: '.js'}))
        .pipe(ts({
            module: 'amd',
            // outFile:'script/app_all.js'
        }))
        .pipe(gulp.dest(outPath+config.ts.path));
});

gulp.task('scss', function (callback) {
    return gulp.src(config.scss.src)
        // .pipe(sourcemaps.init())
        .pipe(isBuild ?
            base64({maxImageSize:12*1024}):
            gutil.noop()
        )
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['> 3%'],
            cascade: true,
            remove: true
        }))
        .pipe(isBuild ? minifyCss() : gutil.noop())
        // .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest((isBuild ? config.buildPath : config.distPath)+config.scss.path));
});
gulp.task('copy-scripts', function (callback) {
    return gulp.src(config.scripts.src)
        .pipe(gulp.dest((isBuild ? config.buildPath : config.distPath)+config.scripts.path));
});

gulp.task('watch', function (callback) {
    //运行一次啊
    gulp.start(['scss','ts','copy-scripts','image']);

    //监听生产环境目录变化
    gulp.watch(config.scss.watch, ['scss']);
    gulp.watch(config.ts.watch, ['ts']);
    gulp.watch(config.image.watch, ['image']);
    return 1;
});
gulp.task('image', function (callback) {
    return gulp.src(config.image.src)
        .pipe(isBuild ? imageMin({
                progressive: true
            }) : gutil.noop())
        .pipe(gulp.dest((isBuild ? config.buildPath : config.distPath) + config.image.path));
});
gulp.task('cleanBuild', function () {
    return gulp.src(config.buildPath, {read: false})
        .pipe(clean());
});

gulp.task('js-build', function (cb) {
    return gulp.src(config.ts.src)
        .pipe(ts({}))
        // .pipe(gulp.src(config.scripts.src))
        // .pipe(order(config.scriptOrder))
        // .pipe(concat('main.js'))
        // .pipe(uglify())
        .pipe(gulp.dest(config.buildPath+config.concatJs.dist));
});

gulp.task('concat-js', function (cb) {
    return gulp.src(config.concatJs.src)
        .pipe(concat('main.js'))
        // .pipe(uglify())
        .pipe(gulp.dest(config.buildPath+config.concatJs.dist));
});

gulp.task('build',function (callback) {
    isBuild=true;
    return gulpSequence(
        'cleanBuild',
        ['image','scss'],
        // ['js-build'],
        ['ts','copy-scripts'],
        // ['concat-js'],
        callback
    );
});