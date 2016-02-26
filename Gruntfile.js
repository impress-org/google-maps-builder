module.exports = function ( grunt ) {

// Load multiple grunt tasks using globbing patterns
	require( 'load-grunt-tasks' )( grunt );

// Project configuration.
	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),

		checktextdomain: {
			options: {
				text_domain       : 'google-maps-builder',
				create_report_file: true,
				keywords          : [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,3,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d',
					' __ngettext:1,2,3d',
					'__ngettext_noop:1,2,3d',
					'_c:1,2d',
					'_nc:1,2,4c,5d'
				]
			},
			files  : {
				src   : [
					'**/*.php', // Include all files
					'!node_modules/**', // Exclude node_modules/
					'!build/.*'// Exclude build/
				],
				expand: true
			}
		},

		makepot: {
			target: {
				options: {
					domainPath     : '/languages/',    // Where to save the POT file.
					exclude        : ['includes/libraries/.*', '.js'],
					mainFile       : 'google-maps-builder.php',    // Main project file.
					potFilename    : 'google-maps-builder.pot',    // Name of the POT file.
					potHeaders     : {
						poedit                 : true,                 // Includes common Poedit headers.
						'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
					},
					type           : 'wp-plugin',    // Type of project (wp-plugin or wp-theme).
					updateTimestamp: true,    // Whether the POT-Creation-Date should be updated without other changes.
					processPot     : function ( pot, options ) {
						pot.headers['report-msgid-bugs-to'] = 'https://wordimpress.com/';
						pot.headers['last-translator'] = 'WP-Translations (http://wp-translations.org/)';
						pot.headers['language-team'] = 'WP-Translations <wpt@wp-translations.org>';
						pot.headers['language'] = 'en_US';
						var translation, // Exclude meta data from pot.
							excluded_meta = [
								'Plugin Name of the plugin/theme',
								'Plugin URI of the plugin/theme',
								'Author of the plugin/theme',
								'Author URI of the plugin/theme'
							];
						for ( translation in pot.translations[''] ) {
							if ( 'undefined' !== typeof pot.translations[''][translation].comments.extracted ) {
								if ( excluded_meta.indexOf( pot.translations[''][translation].comments.extracted ) >= 0 ) {
									console.log( 'Excluded meta: ' + pot.translations[''][translation].comments.extracted );
									delete pot.translations[''][translation];
								}
							}
						}
						return pot;
					}
				}
			}
		},

		exec: {
			txpull  : { // Pull Transifex translation - grunt exec:txpull
				cmd: 'tx pull -a -f --minimum-perc=1' // Change the percentage with --minimum-perc=yourvalue
			},
			txpush_s: { // Push pot to Transifex - grunt exec:txpush_s
				cmd: 'tx push -s'
			},
            composer_release_update: {
                cmd: 'composer update --no-dev --optimize-autoloader --prefer-dist'
            },
            composer_update: {
                cmd: 'composer update'
            }
		},

		dirs: {
			lang: 'languages'
		},

		potomo: {
			dist: {
				options: {
					poDel: true
				},
				files  : [{
					expand: true,
					cwd   : '<%= dirs.lang %>',
					src   : ['*.po'],
					dest  : '<%= dirs.lang %>',
					ext   : '.mo',
					nonull: true
				}]
			}
		},

        clean: {
            post_build: [
                'build/'
            ],
            pre_compress: [
                'build/google-maps-builder/build/'
            ]
        },

        compress: {
            main: {
                options: {
                    mode: 'zip',
                    archive: 'releases/<%= pkg.name %>-<%= pkg.version %>.zip'
                },
                expand: true,
                cwd: 'build/',
                src: [
                    '**/*',
                    '!build/*'
                ]
            }
        },

        copy: {
            build: {
                options: {
                    mode: true
                },
                src: [
                    '**',
                    '!node_modules/**',
                    '!releases',
                    '!releases/**',
                    '!.git/**',
                    '!Gruntfile.js',
                    '!GulpFile.js',
                    '!package.json',
                    '!.gitignore',
                    '!.gitmodules',
                    '!.gitattributes',
                    '!composer.lock',
                    '!.scrutinizer.yml',
                    '!grunt-instructions.md'
                ],
                dest: 'build/<%= pkg.name %>/'
            }
        },

        gitadd: {
            add_zip: {
                options: {
                    force: true
                },
                files: {
                    src: [ 'releases/<%= pkg.name %>-<%= pkg.version %>.zip' ]
                }
            }
        },
        gittag: {
            addtag: {
                options: {
                    tag: '<%= pkg.version %>',
                    message: 'Version <%= pkg.version %>'
                }
            }
        },
        gitcommit: {
            commit: {
                options: {
                    message: 'Version <%= pkg.version %>',
                    noVerify: true,
                    noStatus: false,
                    allowEmpty: true
                },
                files: {
                    src: [ 'package.json', 'ingot.php', 'releases/<%= pkg.name %>-<%= pkg.version %>.zip' ]
                }
            }
        },
        gitpush: {
            push: {
                options: {
                    tags: true,
                    remote: 'origin',
                    branch: 'master'
                }
            }
        },
        replace: {
            core_file: {
                src: [ 'google-maps-builder.php' ],
                overwrite: true,
                replacements: [{
                    from: /Version:\s*(.*)/,
                    to: "Version: <%= pkg.version %>"
                }, {
                    from: /define\(\s*'GMB_VERSION',\s*'(.*)'\s*\);/,
                    to: "define( 'GMB_VERSION', '<%= pkg.version %>' );"
                }]
            }
        }




	} );

// Default task. - grunt makepot
	grunt.registerTask( 'default', 'makepot' );

// Makepot and push it on Transifex task(s).
	grunt.registerTask( 'tx-push', ['makepot', 'exec:txpush_s'] );

// Pull from Transifex and create .mo task(s).
	grunt.registerTask( 'tx-pull', ['exec:txpull', 'potomo'] );

// Setup dev environment, not doing much now, but when we add bower and such it will.
    grunt.registerTask( 'setup-dev', [ 'exec:composer_update' ] );


//release tasks
    grunt.registerTask( 'version_number', [ 'replace:core_file' ] );
    grunt.registerTask( 'pre_vcs', [ 'exec:composer_release_update', 'tx-push', 'version_number', 'copy', 'clean:pre_compress', 'compress' ] );
    grunt.registerTask( 'do_git', [ 'gitadd', 'gitcommit', 'gittag', 'gitpush' ] );
    grunt.registerTask( 'just_build', [  'exec:composer_release_update', 'copy', 'clean:pre_compress', 'compress', 'clean:post_build' ] );
    grunt.registerTask( 'install', [ 'shell:activate' ] );

    grunt.registerTask( 'release', [ 'pre_vcs', 'do_git', 'clean:post_build' ] );
};
