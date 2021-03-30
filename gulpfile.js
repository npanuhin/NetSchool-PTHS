var
    changedInPlace  = require('gulp-changed-in-place'),
    ext_replace     = require('gulp-ext-replace'),
    sourcemaps      = require('gulp-sourcemaps'),
    livereload      = require('gulp-livereload'),
    rename          = require('gulp-rename'),
    // newer           = require('gulp-newer'),
    // debug           = require('debug'),
    Path            = require('path'),
    gulp            = require('gulp'),

    // SCSS:
    media_queries   = require('gulp-group-css-media-queries'),
    autoprefixer    = require('gulp-autoprefixer'),
    // shorthand       = require('gulp-shorthand'),
    cleancss        = require('gulp-clean-css'),
    sass            = require('gulp-dart-sass'),
    // sass            = require('gulp-sass'),
    // postcss         = require('gulp-postcss'),
    // doiuse          = require('gulp-doiuse'),

    // JS:
    uglify_js       = require('gulp-uglify-es').default,
    // babel           = require('gulp-babel'),
    // jscs            = require('gulp-jscs'),
    // terser          = require('gulp-terser'),

    // PHP, HTML
    minify_php      = require('gulp-php-minify'),
    htmlmin         = require('gulp-htmlmin'),

    // Clean:
    clean           = require('gulp-clean'),

    files = {
        'scss':  ['/nosuchfileexists'],
        'html':  ['/nosuchfileexists'],
        'css':   ['/nosuchfileexists'],
        'php':   ['/nosuchfileexists'],
        'js':    ['/nosuchfileexists'],
        'clean': ['/nosuchfileexists']
    };

    files['html'].push('fails/*.max.html');
    files['html'].push('src/*.max.html');

    files['php'].push('*.max.php');
    files['php'].push('fails/*.max.php');
    files['php'].push('src/*.max.php');
    files['php'].push('src/lib/*.max.php');
    files['php'].push('src/online_media/*.max.php');
    files['php'].push('login/*.max.php');
    files['php'].push('diary/*.max.php');
    files['php'].push('announcements/*.max.php');
    files['php'].push('timetable/*.max.php');
    files['php'].push('delete/*.max.php');
    files['php'].push('help/*.max.php');
    files['php'].push('stats/*.max.php');

    files['scss'].push('*.scss');
    files['scss'].push('src/*.scss');
    files['scss'].push('src/styles/*.scss');
    files['scss'].push('src/online_media/*.scss');
    files['scss'].push('login/*.scss');
    files['scss'].push('diary/*.scss');
    files['scss'].push('announcements/*.scss');
    files['scss'].push('timetable/*.scss');
    files['scss'].push('help/*.scss');
    files['scss'].push('fails/*.scss');
    files['scss'].push('stats/*.scss');

    files['js'].push('*.js');
    files['js'].push('src/*.js');
    files['js'].push('src/online_media/*.js');
    files['js'].push('login/*.js');
    files['js'].push('diary/*.js');
    files['js'].push('announcements/*.js');
    files['js'].push('timetable/*.js');
    files['js'].push('help/*.js');
    files['js'].push('stats/*.js');
    files['js'].push('!gulpfile.js');

    files['clean'].push('**/build');
    files['clean'].push('**/*.php');
    files['clean'].push('!src/lib/*.php');
    files['clean'].push('!**/*.max.php');
    files['clean'].push('**/*.html');
    files['clean'].push('!**/*.max.html');


var
    htmlmin_settings = {
        caseSensitive: true,
        collapseInlineTagWhitespace: false,
        collapseWhitespace: true,
        conservativeCollapse: false,
        continueOnParseError: true,
        quoteCharacter: '"',
        removeAttributeQuotes: false,
        removeComments: true,
        removeStyleLinkTypeAttributes: true,
        decodeEntities: true
    }


// ================================= HTML =================================

gulp.task('build-html', function () {
    return gulp.src(files['html'], {allowEmpty: true})

        .pipe(htmlmin(htmlmin_settings))
        .pipe(ext_replace('.html', '.max.html'))
        .pipe(gulp.dest(function (file) {
            return Path.parse(file.path).dir;
            // return Path.join(Path.parse(file.path).dir, 'build');
        }))
});

gulp.task('reload-html', function () {
    return gulp.src(files['html'], {allowEmpty: true})
        .pipe(changedInPlace())

        .pipe(htmlmin(htmlmin_settings))
        .pipe(ext_replace('.html', '.max.html'))
        .pipe(gulp.dest(function (file) {
            return Path.parse(file.path).dir;
            // return Path.join(Path.parse(file.path).dir, 'build');
        }))

    .pipe(livereload())
});


// ================================= PHP ==================================

gulp.task('build-php', function () {
    return gulp.src(files['php'], {allowEmpty: true})
       
        .pipe(minify_php())
        .pipe(htmlmin(htmlmin_settings))
        .pipe(ext_replace('.php', '.max.php'))
        .pipe(gulp.dest(function(file) {
            return Path.parse(file.path).dir;
            // return Path.join(Path.parse(file.path).dir, 'build');
        }))
});

gulp.task('reload-php', function () {
    return gulp.src(files['php'], {allowEmpty: true})
        .pipe(changedInPlace())

        .pipe(minify_php())
        .pipe(htmlmin(htmlmin_settings))
        .pipe(ext_replace('.php', '.max.php'))
        .pipe(gulp.dest(function(file) {
            return Path.parse(file.path).dir;
            // return Path.join(Path.parse(file.path).dir, 'build');
        }))

    .pipe(livereload())
});


// ================================= CSS ==================================


gulp.task('build-css', function () {
    return gulp.src(files['css'], {allowEmpty: true})

        .pipe(cleancss({compatibility: 'ie8'}))
        .pipe(ext_replace('.css', '.max.css'))
        .pipe(ext_replace('.min.css', '.css'))
        .pipe(gulp.dest(function(file) {
            return Path.parse(file.path).dir;
            // return Path.join(Path.parse(file.path).dir, 'build');
        }))
});

gulp.task('reload-css', function () {
    return gulp.src(files['css'], {allowEmpty: true})
        .pipe(changedInPlace())

        .pipe(cleancss({compatibility: 'ie8'}))
        .pipe(ext_replace('.css', '.max.css'))
        .pipe(ext_replace('.min.css', '.css'))
        .pipe(gulp.dest(function(file) {
            return Path.parse(file.path).dir;
            // return Path.join(Path.parse(file.path).dir, 'build');
        }))

    .pipe(livereload())
});


// ================================= SCSS =================================

gulp.task('build-scss', function () {
    return gulp.src(files['scss'], {allowEmpty: true})

        .pipe(sass({
            includePaths: ['C:\\OpenServer\\node_modules'],
            includePaths: ['C:\\OpenServer\\domains\\node_modules'],
        }).on('error', sass.logError))
        .pipe(autoprefixer({
            brousers: ['last 3 versions', '> 5%'],
            cascade: false
        }))
        .pipe(media_queries())
        // .pipe(shorthand())
        // .pipe(gulp.dest(function (file) {
        //     // return Path.parse(file.path).dir;
        //     return Path.join(Path.parse(file.path).dir, 'build');
        // }))
        .pipe(cleancss({compatibility: 'ie8'}))
        .pipe(ext_replace('.css', '.max.css'))
        .pipe(ext_replace('.min.css', '.css'))
        .pipe(gulp.dest(function (file) {
            // return Path.parse(file.path).dir;
            return Path.join(Path.parse(file.path).dir, 'build');
        }))
});

gulp.task('reload-scss', function () {
    return gulp.src(files['scss'], {allowEmpty: true})
        // .pipe(changedInPlace())

        .pipe(sass({
            includePaths: ['C:\\OpenServer\\node_modules'],
            includePaths: ['C:\\OpenServer\\domains\\node_modules'],
        }).on('error', sass.logError))
        .pipe(autoprefixer({
            brousers: ['last 3 versions', '> 5%'],
            cascade: false
        }))
        .pipe(media_queries())
        // .pipe(shorthand())
        // .pipe(gulp.dest(function (file) {
        //     // return Path.parse(file.path).dir;
        //     return Path.join(Path.parse(file.path).dir, 'build');
        // }))
        .pipe(cleancss({compatibility: 'ie8'}))
        .pipe(ext_replace('.css', '.max.css'))
        .pipe(ext_replace('.min.css', '.css'))
        .pipe(gulp.dest(function (file) {
            // return Path.parse(file.path).dir;
            return Path.join(Path.parse(file.path).dir, 'build');
        }))

    .pipe(livereload())
});


// ================================== JS ==================================

gulp.task('build-js', function () {
    return gulp.src(files['js'], {allowEmpty: true})

        // .pipe(babel({
        //     presets: [
        //         [
        //             '@babel/env',
        //             // {
        //             //     // 'ie8': true,
        //             //     'keep_classnames': true,
        //             //     'keep_fnames': true,
        //             //     'safari10': true
        //             // }
        //         ]
        //     ],
            
        // }))
        // .pipe(terser())
        .pipe(uglify_js())
        .pipe(ext_replace('.js', '.max.js'))
        .pipe(ext_replace('.min.js', '.js'))
        .pipe(gulp.dest(function(file) {
            // return Path.parse(file.path).dir;
            return Path.join(Path.parse(file.path).dir, 'build');
        }))
        // .pipe(jscs())
        // .pipe(jscs.reporter())
});

gulp.task('reload-js', function () {
    return gulp.src(files['js'], {allowEmpty: true})
        .pipe(changedInPlace())

        // .pipe(babel({
        //     presets: [
        //         [
        //             '@babel/env',
        //             // {
        //             //     // 'ie8': true,
        //             //     'keep_classnames': true,
        //             //     'keep_fnames': true,
        //             //     'safari10': true
        //             // }
        //         ]
        //     ],
            
        // }))
        // .pipe(terser())
        .pipe(uglify_js())
        .pipe(ext_replace('.js', '.max.js'))
        .pipe(ext_replace('.min.js', '.js'))
        .pipe(gulp.dest(function(file) {
            // return Path.parse(file.path).dir;
            return Path.join(Path.parse(file.path).dir, 'build');
        }))
        // .pipe(jscs())
        // .pipe(jscs.reporter())

    .pipe(livereload())
});


// ================================ CLEAN ================================

gulp.task('clean', function () {
    return gulp.src(files['clean'], {allowEmpty: true, read: false}).pipe(clean());
});


// ================================ MAIN ================================

gulp.task('build', gulp.parallel('build-html', 'build-php', 'build-css', 'build-scss', 'build-js'));
gulp.task('deploy', gulp.series('clean', 'build'));

gulp.task('default', function () {
    livereload.listen();
    gulp.watch(files['html'], gulp.series('reload-html'));
    gulp.watch(files['php'], gulp.series('reload-php'));
    gulp.watch(files['css'], gulp.series('reload-css'));
    gulp.watch(files['scss'], gulp.series('reload-scss'));
    gulp.watch(files['js'], gulp.series('reload-js'));
});
