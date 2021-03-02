var
    Path            = require('path'),
    gulp            = require('gulp'),
    // debug           = require('debug'),
    rename          = require('gulp-rename'),
    livereload      = require('gulp-livereload'),
    sourcemaps      = require('gulp-sourcemaps'),
    changedInPlace  = require('gulp-changed-in-place'),
    ext_replace     = require('gulp-ext-replace')

    // SCSS:
    // sass            = require('gulp-sass'),
    sass            = require('gulp-dart-sass'),
    autoprefixer    = require('gulp-autoprefixer'),
    // shorthand       = require('gulp-shorthand'),
    gcmq            = require('gulp-group-css-media-queries'),
    cleancss        = require('gulp-clean-css'),
    csso            = require('gulp-csso'),

    // JS:
    uglify_js       = require('gulp-uglify-es').default,
    // jscs            = require('gulp-jscs'),
    // changed         = require('gulp-changed'),
    // babel           = require('gulp-babel'),
    terser          = require('gulp-terser'),

    // PHP, HTML
    htmlmin         = require('gulp-htmlmin'),
    minify_php      = require('gulp-php-minify'),

    files = {
        'scss': [],
        'html': [],
        'css': [],
        'php': [],
        'js': []
    };

    files['php'].push('*.max.php');
    files['php'].push('src/*.max.php');
    files['php'].push('login/*.max.php');
    files['php'].push('marks/*.max.php');
    files['php'].push('announcements/*.max.php');
    files['php'].push('timetable/*.max.php');
    files['php'].push('delete/*.max.php');

    files['scss'].push('*.scss');
    files['scss'].push('src/*.scss');
    files['scss'].push('src/styles/*.scss');
    files['scss'].push('login/*.scss');
    files['scss'].push('marks/*.scss');
    files['scss'].push('announcements/*.scss');
    files['scss'].push('timetable/*.scss');
    files['scss'].push('help/*.scss');
    files['scss'].push('fails/*.scss');

    files['js'].push('*.js');
    files['js'].push('src/*.js');
    files['js'].push('login/*.js');
    files['js'].push('marks/marks.js');
    files['js'].push('announcements/*.js');
    files['js'].push('timetable/*.js');

    files['js'].push('!gulpfile.js');


gulp.task('reload-html', function(){
    return gulp.src(files['html'])
    .pipe(changedInPlace())

    .pipe(htmlmin({
        caseSensitive: true,
        collapseInlineTagWhitespace: true,
        collapseWhitespace: true,
        conservativeCollapse: false,
        continueOnParseError: true,
        quoteCharacter: '"',
        removeAttributeQuotes: false,
        removeComments: true,
        removeStyleLinkTypeAttributes: true,
        decodeEntities: true
    }))

    .pipe(livereload());
});

gulp.task('reload-php', function(){
    return gulp.src(files['php'])
    .pipe(changedInPlace())

    .pipe(minify_php())
    .pipe(htmlmin({
        caseSensitive: true,
        collapseInlineTagWhitespace: true,
        collapseWhitespace: true,
        conservativeCollapse: false,
        continueOnParseError: true,
        quoteCharacter: '"',
        removeAttributeQuotes: false,
        removeComments: true,
        removeStyleLinkTypeAttributes: true,
        decodeEntities: true
    }))

    .pipe(ext_replace('.php', '.max.php'))
    .pipe(gulp.dest(function(file){
        return Path.parse(file.path).dir;
    }))
    .pipe(livereload());
});

gulp.task('reload-css', function(){
    return gulp.src(files['css'])
    .pipe(changedInPlace())

    .pipe(livereload());
});

gulp.task('reload-scss', function(){
    let filepath = '';
    return gulp.src(files['scss'])
    // .pipe(changedInPlace())

    // .pipe(sourcemaps.init({largeFile: true}))

    .pipe(sass({
        includePaths: ['C:\\OSPanel\\node_modules'],
        includePaths: ['C:\\OpenServer\\node_modules'],
    }).on('error', sass.logError))
    .pipe(autoprefixer({
        brousers: ['last 3 versions', '> 5%'],
        cascade: false
    }))
    .pipe(gcmq())
    // .pipe(shorthand())

    // .pipe(sourcemaps.write())

    .pipe(rename(function(path){
        filepath = path.dirname;
        path.dirname = '';
    }))
    .pipe(gulp.dest(function(file){
        return Path.join(Path.parse(file.path).dir, filepath, 'build');
    }))

    .pipe(cleancss({compatibility: 'ie8'}))

    // .pipe(sourcemaps.write())

    .pipe(ext_replace('.min.css', '.css'))
    .pipe(gulp.dest(function(file){
        return Path.parse(file.path).dir;
    }))

    .pipe(livereload());
});

gulp.task('reload-js', function(){
    let filepath = '';
    return gulp.src(files['js'])
    .pipe(changedInPlace())

    .pipe(rename(function(path){
        console.log(path);
        filepath = path.dirname;
        path.dirname = '';
    }))

    // .pipe(sourcemaps.init({largeFile: true}))

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

    // .pipe(sourcemaps.write())

    .pipe(ext_replace('.min.js', '.js'))
    .pipe(rename(function(path){
        filepath = path.dirname;
        path.dirname = '';
    }))
    .pipe(gulp.dest(function(file){
        console.log(Path.join(Path.parse(file.path).dir, filepath, 'build'));
        return Path.join(Path.parse(file.path).dir, filepath, 'build');
    }))

    // .pipe(jscs())
    // .pipe(jscs.reporter())

    .pipe(livereload());
});


gulp.task('default', function(){
    livereload.listen();
    gulp.watch(files['html'], gulp.series('reload-html'));
    gulp.watch(files['php'], gulp.series('reload-php'));
    gulp.watch(files['css'], gulp.series('reload-css'));
    gulp.watch(files['scss'], gulp.series('reload-scss'));
    gulp.watch(files['js'], gulp.series('reload-js'));
});
