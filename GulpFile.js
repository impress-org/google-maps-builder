/**
 * Maps Builder Gulp File
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
	frontend_styles : ['./assets/scss/frontend/maps-builder.scss'],
	scripts         : ['./assets/js/**/*.js', '!./assets/js/**/*.min.js'],
	frontend_scripts: [
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


/* Frontend SCSS Task
 ------------------------------------- */
gulp.task( 'frontend_styles', function () {
	return gulp.src( source_paths.frontend_styles )
		.pipe( sourcemaps.init() ) //start up sourcemapping
		.pipe( autoprefixer() ) //add prefixes for older browsers
		.pipe( sass( {
			errLogToConsole: true
		} ) ) //compile SASS; ensure any errors don't stop gulp watch
		.pipe( rename( 'google-maps-builder.css' ) ) //rename for our main un-minified file
		.pipe( sourcemaps.write( '.' ) ) //write SCSS source maps to the appropriate plugin dir
		.pipe( gulp.dest( './assets/css' ) ) //place compiled file in appropriate directory
		.pipe( rename( 'google-maps-builder.min.css' ) ) //rename for our minified version
		.pipe( minifyCSS() ) //actually minify the file
		.pipe( sourcemaps.write( '' ) ) //write SCSS source maps to the appropriate plugin dir
		.pipe( gulp.dest( './assets/css' ) ) //place the minified compiled file
		.pipe( livereload() ) //reload browser
		.pipe( notify( {
			message: 'Frontend styles task complete!',
			onLast : true //notify developer: only notify on completion of task (prevents multiple notifications per file)
		} ) );
} );


/* JS
 ------------------------------------- */
gulp.task( 'scripts', function () {
	return gulp.src( source_paths.scripts )
		.pipe( uglify( {
			preserveComments: 'false'
		} ) )
		.pipe( rename( {suffix: ".min"} ) )
		.pipe( gulp.dest( 'assets/js' ) )
		.pipe( notify( {
			message: 'Scripts task complete!',
			onLast : true //only notify on completion of task (prevents multiple notifications per file)
		} ) );
		//.pipe( livereload() );
} );


/* Watch Files For Changes
 ------------------------------------- */
gulp.task( 'watch', function () {

	//Start up livereload on this biz
	livereload.listen();

	//Add watching on Admin SCSS-files
	gulp.watch( 'assets/scss/admin/*.scss', function () {
		gulp.start( 'admin_styles' );
	} );

	//Add watching on Frontend SCSS-files
	//gulp.watch( 'assets/scss/frontend/*.scss', function () {
	//	gulp.start( 'frontend_styles' );
	//} );

	//Add watching on JS files
	gulp.watch( source_paths.scripts, ['scripts'] );

	//Add watching on template-files
	gulp.watch( 'templates/*.php', function () {
		livereload(); //and reload when changed
	} );

} );

/* Handle errors elegantly with gulp-notify
 ------------------------------------- */
var onError = function ( err ) {
	gutil.log( '======= ERROR. ========\n' );
	notify.onError( "ERROR: " + err.plugin )( err ); // for growl
	gutil.beep();
	this.end();
};



/* Default Gulp task
 ------------------------------------- */
gulp.task( 'default', function () {
	gulp.start( 'admin_styles', 'frontend_styles', 'scripts', 'watch' );
	notify( {message: 'Default task complete'} )
} );