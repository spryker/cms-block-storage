var config = require('../config.js');
var gulp = require('gulp');
var del = require('del');
var hb = require('gulp-hb');
var browserSync = require('browser-sync');
var reload = browserSync.reload;

gulp.task('templates', function(done) {
  // del([config.paths.dest.templates + '/**/*.html']);
  return gulp
    .src('./' + config.paths.source.templates)
    .pipe(hb({
      partials: [config.paths.source.partials],
      bustCache: true,
      debug: true
    }))
    .pipe(gulp.dest(config.paths.dest.templates))
    .pipe(browserSync.reload({stream:true}));
});
