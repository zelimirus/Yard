module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		less: {
			production: {
				cleancss: true,
				ieCompat: true,
				compress : true,
				files: {
					"css/custom.min.css": "less/main.less"
				}
			}
		},
		cssmin: {
			css: {
				src: "css/custom.min.css",
				dest: "css/custom.min.css"
			}
		}
	});

	//grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-cssmin');

	grunt.registerTask('build', ['less:production', 'cssmin:css']);
};