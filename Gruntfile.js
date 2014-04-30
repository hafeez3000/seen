module.exports = function(grunt) {
	var jsFiles = [
		"web/components/bootstrap/dist/js/bootstrap.min.js",
		"web/components/select2/select2.min.js",
		"web/components/spin.js/spin.js",
		"web/components/moment/min/moment-with-langs.min.js",
		"web/components/holderjs/holder.js",
		"web/js/include/app.js"
	];

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),
		uglify: {
			development: {
				options: {
					compress: {
						warnings: false
					},
					sourceMap: true,

				},
				files: {
					"web/js/app.min.js": jsFiles,
					"web/js/jquery.min.js": "web/components/jquery/dist/jquery.min.js"
				}
			},
			production: {
				options: {
					compress: {
						warnings: false
					}
				},
				files: {
					"web/js/app.min.js": jsFiles,
					"web/js/jquery.min.js": "web/components/jquery/dist/jquery.min.js"
				}
			}
		},
		less: {
			development: {
				options: {
					compress: true,
					sourceMap: true,
					sourceMapFilename: "web/less/app.source.less"
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
					"web/components/select2/select2.css",
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
		},
		copy: {
			select_png: {
				expand: true,
				cwd: "web/components/select2/",
				src: "*.png",
				dest: "web/css"
			},
			select_gif: {
				expand: true,
				cwd: "web/components/select2/",
				src: "*.gif",
				dest: "web/css"
			},
			fonts: {
				expand: true,
				cwd: "web/components/bootstrap/fonts/",
				src: "**",
				dest: "web/fonts"
			}
		}
	});

	grunt.loadNpmTasks("grunt-contrib-uglify");
	grunt.loadNpmTasks("grunt-contrib-less");
	grunt.loadNpmTasks("grunt-contrib-concat");
	grunt.loadNpmTasks("grunt-contrib-watch");
	grunt.loadNpmTasks("grunt-contrib-copy");

	grunt.registerTask("build", ["uglify:development", "less:development", "concat:app", "copy"]);
	grunt.registerTask("build_production", ["uglify:production", "less:production", "concat", "copy"]);

	grunt.registerTask("default", ["build"]);

};