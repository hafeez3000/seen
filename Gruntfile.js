module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),
		uglify: {
			development: {
				options: {
					compress: true,
					sourceMap: true
				},
				files: {
					"web/js/app.min.js": "web/js/include/app.js"
				}
			},
			production: {
				options: {
					compress: true
				},
				files: {
					"web/js/app.min.js": "web/js/include/app.js"
				}
			}
		},
		less: {
			development: {
				options: {
					compress: true,
					sourceMap: true
				},
				files: {
					"web/css/app.min.css": "web/less/app.less"
				}
			},
			production: {
				options: {
					compress: true
				},
				files: {
					"web/css/app.min.css": "web/less/app.less"
				}
			}
		},
		concat: {
			app: {
				src: [
					"web/components/bootswatch/lumen/bootstrap.min.css",
					"web/css/app.min.css"
				],
				dest: "web/css/app.min.css"
			}
		},
		watch: {
			css: {
				files: [
					"web/less/*.less",
					"web/components/**/*.less",
					"web/components/**/*.css"
				],
				tasks: [
					"less:development",
					"concat"
				]
			},
			js: {
				files: [
					"web/js/include/**/*.js",
					"web/components/**/*.js"
				],
				tasks: [
					"uglify:development"
				]
			}
		}
	});

	grunt.loadNpmTasks("grunt-contrib-uglify");
	grunt.loadNpmTasks("grunt-contrib-less");
	grunt.loadNpmTasks("grunt-contrib-concat");
	grunt.loadNpmTasks("grunt-contrib-watch");

	grunt.registerTask("build", ["uglify:development", "less:development", "concat:app"]);
	grunt.registerTask("build_production", ["uglify:production", "less:production", "concat"]);

	grunt.registerTask("default", ["build"]);

};