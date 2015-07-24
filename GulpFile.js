/**
 * Google Maps Builder Gulp File
 * @description Compiles SCSS, minifies scripts, renames files, and many other useful tasks; using Gulp.js
 * @since 2.0
 */

/* Modules (Can be installed with npm install command using package.json)
 ------------------------------------- */
var gulp = require( 'gulp' ),
	uglify = require( 'gulp-uglify' ),
	gutil = require( 'gulp-util' ),
	rename = require( 'gulp-rename' ),
	watch = require( 'gulp-watch' ),
	minifyCss = require( 'gulp-minify-css' ),
	sourcemaps = require( 'gulp-sourcemaps' ),
	autoprefixer = require( 'gulp-autoprefixer' ),
	livereload = require( 'gulp-livereload' ),
	del = require( 'del' ),
	sass = require( 'gulp-sass' ),
	concat = require( 'gulp-concat' ),
	notify = require( 'gulp-notify' ),
	minifyCSS = require( 'gulp-minify-css' );


/* Old Paths
 ------------------------------------- */
var old_source_paths = {
	scripts: ['assets/js/*.js', '!assets/js/*.min.js'],
	styles : ['assets/css/*.css', '!assets/css/*.min.css']
};


/* Paths
 ------------------------------------- */
var source_paths = {
	admin_styles    : ['./assets/scss/**/gmb-admin.scss'],
	plugin_styles    : ['./assets/scss/**/*.scss'],
	frontend_styles : ['./assets/scss/frontend/give-frontend.scss'],
	scripts         : ['./assets/js/**/*.js', '!./assets/js/**/*.min.js'],
	frontend_scripts: [
		'./assets/js/plugins/jQuery.blockUI.min.js',
		'./assets/js/plugins/jquery.qtip.min.js',
		'./assets/js/plugins/jquery.maskMoney.min.js',
		'./assets/js/plugins/give-magnific.min.js',
		'./assets/js/frontend/*.min.js' //Frontend scripts need to be loaded last
	]
};


/* Admin SCSS Task
 ------------------------------------- */
gulp.task( 'admin_styles', function () {
	return gulp.src( source_paths.admin_styles )
		.pipe( sourcemaps.init() )
		.pipe( autoprefixer() )
		.pipe( sass( {
			errLogToConsole: true
		} ) )
		.pipe( rename( 'gmb-admin.css' ) )
		.pipe( sourcemaps.write( '.' ) )
		.pipe( gulp.dest( './assets/css' ) )
		.pipe( rename( 'gmb-admin.min.css' ) )
		.pipe( minifyCSS() )
		.pipe( sourcemaps.write() )
		.pipe( gulp.dest( './assets/css' ) )
		.pipe( livereload() )
		.pipe( notify( {
			message: 'Admin styles task complete!',
			onLast : true //only notify on completion of task
		} ) );
} );


/* Handle errors elegantly with gulp-notify
 ------------------------------------- */
var onError = function ( err ) {
	gutil.log( '======= ERROR. ========\n' );
	notify.onError( "ERROR: " + err.plugin )( err ); // for growl
	gutil.beep();
	this.end();
};


//Old:
//gulp.task( 'scripts_styles', function () {
//	gulp.src( source_paths.scripts )
//		.pipe( uglify() )
//		.pipe( rename( {
//			extname: '.min.js'
//		} ) )
//		.pipe( gulp.dest( 'assets/js' ) );
//
//	gulp.src( source_paths.styles )
//		.pipe( minifyCss( {compatiability: 'ie8'} ) )
//		.pipe( rename( {
//			extname: '.min.css'
//		} ) )
//		.pipe( gulp.dest( 'assets/css' ) );
//
//} );

//gulp.task( 'watch', function () {
//	gulp.watch( 'admin/assets/js/*.js', ['scripts_styles'] );
//	gulp.watch( 'admin/assets/css/*.css', ['scripts_styles'] );
//} );
//
//gulp.task( 'default', ['scripts_styles'] );

/* Default Gulp task
 ------------------------------------- */
gulp.task( 'default', function () {
	gulp.start( 'admin_styles', 'watch' );
	notify( {message: 'Default task complete'} )
} );