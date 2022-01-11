'use strict';

module.exports = function( grunt ) {
	grunt.initConfig({
		eslint: {
			target: {
				src: [ '**/*.js', '!node_modules/**/*', '!vendor/**/*' ],
				dot: true
			}
		},
		stylelint: {
			target: {
				src: [ '**/*.css', '!node_modules/**/*', '!vendor/**/*' ]
			}
		},
		phpcs: {
			target: {
				src: [ '**/*.php', '!node_modules/**/*', '!vendor/**/*' ]
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
