var gulp = require('gulp');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var watch = require('gulp-watch');
var minifyCss = require('gulp-minify-css');

gulp.task( 'admin', function() {
	gulp.src('admin/assets/js/*.js')
		.pipe(uglify())
		.pipe(rename({
			extname: '.min.js'
		}))
		.pipe(gulp.dest('admin/assets/js/min'));
	
	gulp.src('admin/assets/css/*.css')
		.pipe(minifyCss({compatiability:'ie8'}))
		.pipe(rename({
			extname: '.min.css'
		}))
		.pipe(gulp.dest('admin/assets/css/min'))
		
});

gulp.task( 'public', function() {
	gulp.src('public/assets/js/*.js')
		.pipe(uglify())
		.pipe(rename({
			extname: '.min.js'
		}))
		.pipe(gulp.dest('admin/assets/js/min'));
	
	gulp.src('public/assets/css/*.css')
		.pipe(minifyCss({compatiability:'ie8'}))
		.pipe(rename({
			extname: '.min.css'
		}))
		.pipe(gulp.dest('admin/assets/css/min'))
		
});

gulp.task('default', ['admin', 'public']);