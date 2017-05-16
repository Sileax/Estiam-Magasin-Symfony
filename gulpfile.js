var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var minifyCSS = require('gulp-minify-css');
var util = require('gulp-util');
var plumber = require('gulp-plumber');
var uglify = require('gulp-uglify');
var rev = require('gulp-rev');
var del = require('del');
var q = require('q');

var config = {
    assetsPath: 'app/Resources/assets',
    compiledPath: 'web',
    bowerDir: 'vendor/bower_components',
    sassPattern: 'sass/**/*.scss',
    production: !!util.env.production,
    revManifestPath: 'app/Resources/assets/rev-manifest.json'
};


// Watch should be run by default task only in development
if (config.production) {
    gulp.task('default', ['clean', 'styles', 'scripts', 'fonts']);
} else {
    gulp.task('default', ['clean', 'styles', 'scripts', 'fonts', 'watch']);
}

/**
 * Task for building styles
 *
 * Add the styles to the file you want or add a new file to the queue of files to compile
 */
gulp.task('styles', function () {
    var queue = new TasksQueue();

    queue.queue([
        config.assetsPath +'/sass/main.scss'
    ], 'styles.css');

    return queue.run(app.addStyle);
});


/**
 * Task for building scripts
 *
 * Add the scripts to the file you want or add a new file to the queue of files to compile
 */
gulp.task('scripts', ['styles'], function () {
    var queue = new TasksQueue();

    queue.queue([
        config.assetsPath + '/js/main.js'
    ], 'scripts.js');

    return queue.run(app.addScript);
});


/**
 * Tasks for copying fonts to the public folder
 *
 * Add the fonts you want to copy
 */
gulp.task('fonts', function () {
    app.copy([
        // Write here the path to your fonts
    ], config.compiledPath +'/fonts');
});


/**
 * Clean all files generated during a previous compilation
 */
gulp.task('clean', function () {
    del.sync(config.revManifestPath);
    del.sync('web/css/*');
    del.sync('web/js/*');
    del.sync('web/fonts/*');
});


/**
 * Watch for changes in styles and scripts
 */
gulp.task('watch', function () {
    gulp.watch(config.assetsPath +'/'+ config.sassPattern, ['styles']);
    gulp.watch(config.assetsPath +'/js/**/*.js', ['scripts']);
});


var app = {};


app.addStyle = function (paths, filename) {
    return gulp.src(paths)
        .pipe(plumber())
        .pipe((!config.production) ? sourcemaps.init() : util.noop())
        .pipe(sass())
        .pipe(concat('css/'+ filename))
        .pipe(minifyCSS())
        .pipe(rev())
        .pipe((!config.production) ? sourcemaps.write('.') : util.noop())
        .pipe(gulp.dest(config.compiledPath))
        .pipe(rev.manifest(config.revManifestPath, {
            merge: true
        }))
        .pipe(gulp.dest('.'));
};

app.addScript = function (paths, filename) {
    return gulp.src(paths)
        .pipe(plumber())
        .pipe((!config.production) ? sourcemaps.init() : util.noop())
        .pipe(concat('js/'+ filename))
        .pipe(uglify())
        .pipe(rev())
        .pipe((!config.production) ? sourcemaps.write('.') : util.noop())
        .pipe(gulp.dest(config.compiledPath))
        .pipe(rev.manifest(config.revManifestPath, {
            merge: true
        }))
        .pipe(gulp.dest('.'));
};

app.copy = function (files, outputDir) {
    return gulp.src(files)
        .pipe(gulp.dest(outputDir));
};


/**
 * Object that queues gulp src items and runs them synchronously
 *
 */
var TasksQueue = function() {
    this.queuedItems = [];
};

TasksQueue.prototype.queue = function () {
    this.queuedItems.push(arguments);
};

TasksQueue.prototype.run = function (callback) {
    var queuedItems = this.queuedItems;
    var i = 0;
    var deferred = q.defer();

    runNext();

    return deferred.promise;

    function runNext() {
        if (i >= queuedItems.length) {
            deferred.resolve();
            return ;
        }

        callback.apply(app, queuedItems[i]).on('end', function () {
            i++;
            runNext();
        });
    }
};
