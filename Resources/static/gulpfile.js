let path = require('path'),
    gulp = require('gulp'),
    concat = require('gulp-concat'),
    imagemin = require('gulp-imagemin'),
    autoprefixer = require('autoprefixer'),
    gulpif = require('gulp-if'),
    flatten = require('gulp-flatten'),
    plumber = require('gulp-plumber'),
    changed = require('gulp-changed'),
    postcss = require('gulp-postcss'),
    flexbugs = require('postcss-flexbugs-fixes'),
    csso = require('postcss-csso'),
    webp = require('gulp-webp'),
    del = require('del'),
    sass = require('gulp-sass'),
    watch = require('gulp-watch'),
    browserSync = require('browser-sync').create();

const isProd = process.env.NODE_ENV === 'production';

let settings = {
    scsso: {
        comments: false,
        restructure: false
    },
    sass: {
        includePaths: [
            path.join(__dirname, 'node_modules/flexy-framework')
        ]
    },
    paths: {
        clean: '../public/*',
        webp: './images/**/*{.jpg,.png,.jpeg}',
        images: './images/**/*{.jpg,.png,.jpeg,.gif,.svg}',
        fonts: [
            './fonts/**/*{.eot,.otf,.woff,.woff2,.ttf,.svg}',
            './node_modules/material-design-icons/iconfont/**/*{.eot,.otf,.woff,.woff2,.ttf,.svg}',
        ],
        css: [
            './fonts/**/*.css',
            './scss/**/*.scss'
        ],
        serviceWorker: '../public/**/*',
    },
    dst: {
        serviceWorker: path.resolve('../public'),
        css: path.resolve('../public/css'),
        images: path.resolve('../public/images'),
        fonts: path.resolve('../public/fonts')
    }
};

gulp.task('fonts', () => {
    return gulp.src(settings.paths.fonts)
        .pipe(flatten())
        .pipe(gulp.dest(settings.dst.fonts));
});

gulp.task('image:webp', () => {
    return gulp.src(settings.paths.images)
        .pipe(changed(settings.dst.images))
        .pipe(webp())
        .pipe(gulp.dest(settings.dst.images))
        .pipe(browserSync.stream());
});

gulp.task('image:optimize', () => {
    return gulp.src(settings.paths.images)
        .pipe(changed(settings.dst.images))
        // Alternative use imagemin
        .pipe(gulpif(isProd, imagemin([
            imagemin.gifsicle({ interlaced: true }),
            imagemin.jpegtran({ progressive: true }),
            imagemin.optipng({ optimizationLevel: 5 }),
            imagemin.svgo({ plugins: [{ removeViewBox: false }] })
        ])))
        .pipe(gulp.dest(settings.dst.images))
        .pipe(browserSync.stream());
});

gulp.task('images', ['image:optimize', 'image:webp']);

gulp.task('css', ['fonts'], () => {
    const plugins = [
        flexbugs,
        autoprefixer({
            browsers: [
                '>1%',
                'last 4 versions',
                'Firefox ESR',
                'not ie < 9', // React doesn't support IE8 anyway
            ],
            // cascade: false,
            flexbox: 'no-2009'
        }),
        csso
    ];

    return gulp.src(settings.paths.css)
        .pipe(plumber())
        .pipe(sass(settings.sass).on('error', sass.logError))
        .pipe(postcss(plugins))
        .pipe(concat('admin.bundle.css'))
        .pipe(gulp.dest(settings.dst.css))
        .pipe(browserSync.stream());
});

gulp.task('watch', () => {
    browserSync.init({
        open: false,
        proxy: "localhost:8000"
    });

    watch('../public/js/**/*.js', () => {
        browserSync.reload();
    });
    watch(settings.paths.css, () => {
        gulp.start('css');
    });
    watch(path.join(__dirname, 'node_modules/flexy-framework/flexy/**/*.scss'), () => {
        gulp.start('css');
    });
    watch(settings.paths.fonts, () => {
        gulp.start('fonts', 'css');
    });
    watch(settings.paths.images, () => {
        gulp.start('images');
    });
    watch([
        '../templates/**/*.html',
    ], () => {
        browserSync.reload();
    });
});

gulp.task('clean', () => {
    return del.sync(settings.paths.clean, {
        force: true
    });
});

gulp.task('default', ['css', 'images']);
