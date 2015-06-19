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
	admin_styles    : ['admin/assets/js/*.js', '!admin/assets/js/*.min.js'],
	admin_scripts   : ['admin/assets/css/*.css', '!admin/assets/css/*.min.css'],
	frontend_styles : ['public/assets/js/*.js', '!public/assets/js/*.min.js'],
	frontend_scripts: ['public/assets/css/*.css', '!public/assets/css/*.min.css']
};

gulp.task( 'admin', function () {
	gulp.src( source_paths.admin_styles )
		.pipe( uglify() )
		.pipe( rename( {
			extname: '.min.js'
		} ) )
		.pipe( gulp.dest( 'admin/assets/js' ) );

	gulp.src( source_paths.admin_scripts )
		.pipe( minifyCss( {compatiability: 'ie8'} ) )
		.pipe( rename( {
			extname: '.min.css'
		} ) )
		.pipe( gulp.dest( 'admin/assets/css' ) );

} );

gulp.task( 'public', function () {
	gulp.src( source_paths.frontend_scripts )
		.pipe( uglify() )
		.pipe( rename( {
			extname: '.min.js'
		} ) )
		.pipe( gulp.dest( 'public/assets/js' ) );

	gulp.src( source_paths.frontend_styles )
		.pipe( minifyCss( {compatiability: 'ie8'} ) )
		.pipe( rename( {
			extname: '.min.css'
		} ) )
		.pipe( gulp.dest( 'public/assets/css' ) );

} );

gulp.task( 'watch', function () {
	gulp.watch( 'public/assets/js/*.js', ['public'] );
	gulp.watch( 'public/assets/css/*.css', ['public'] );
	gulp.watch( 'admin/assets/js/*.js', ['admin'] );
	gulp.watch( 'admin/assets/css/*.css', ['admin'] );
} );

gulp.task( 'default', ['admin', 'public'] );