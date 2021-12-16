'use strict';

module.exports = function( grunt ) {
	grunt.initConfig({
		eslint: {
			target: {
				src: [ 'tawkto/**/*.js', '!node_modules/**/*' ],
				dot: true
			}
		},
		stylelint: {
			target: {
				src: [ 'tawkto/**/*.css' ]
			}
		},
		phpcs: {
			target: {
				src: [ 'tawkto/**/*.php' ]
			},
			options: {
				bin: 'vendor/bin/phpcs'
			}
		}
	});

	grunt.loadNpmTasks( 'grunt-eslint' );
	grunt.loadNpmTasks( 'grunt-stylelint' );
	grunt.loadNpmTasks( 'grunt-phpcs' );
	grunt.registerTask( 'default', [ 'eslint', 'stylelint', 'phpcs' ]);
};
