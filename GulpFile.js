/**
 * Google Maps Builder Gulp File
 * @description Compiles SCSS, minifies scripts, renames files, and many other useful tasks; using Gulp.js
 * @since 2.0
 */

/* Modules (Can be installed with npm install command using package.json)
 ------------------------------------- */
var gulp = require( 'gulp' ),
	uglify = require( 'gulp-uglify' ),
	rename = require( 'gulp-rename' ),
	watch = require( 'gulp-watch' ),
	minifyCss = require( 'gulp-minify-css' ),
	notify = require( 'gulp-notify' );


/* Paths
 ------------------------------------- */
var source_paths = {
	scripts : ['assets/js/*.js', '!assets/js/*.min.js'],
	styles: ['assets/css/*.css', '!assets/css/*.min.css']
};

gulp.task( 'scripts_styles', function () {
	gulp.src( source_paths.scripts )
		.pipe( uglify() )
		.pipe( rename( {
			extname: '.min.js'
		} ) )
		.pipe( gulp.dest( 'assets/js' ) );

	gulp.src( source_paths.styles )
		.pipe( minifyCss( {compatiability: 'ie8'} ) )
		.pipe( rename( {
			extname: '.min.css'
		} ) )
		.pipe( gulp.dest( 'assets/css' ) );

} );

gulp.task( 'watch', function () {
	gulp.watch( 'admin/assets/js/*.js', ['scripts_styles'] );
	gulp.watch( 'admin/assets/css/*.css', ['scripts_styles'] );
} );

gulp.task( 'default', ['scripts_styles'] );