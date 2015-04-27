var gulp = require('gulp');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var watch = require('gulp-watch');

gulp.task( 'compress', function() {
	return gulp.src('admin/assets/js/*.js')
		.pipe(uglify())
		.pipe(rename({
			extname: '.min.js'
		}))
		.pipe(gulp.dest('admin/assets/js/min'))
});

gulp.task('watch', function(){
	watch('admin/aseets/js/*.js', ['compress'])
})