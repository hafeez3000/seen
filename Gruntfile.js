module.exports = function(grunt) {
	var jsFiles = [
		"vendor/bower/jquery/dist/jquery.min.js",
		"vendor/bower/bootstrap/dist/js/bootstrap.min.js",
		"vendor/bower/select2/select2.min.js",
		"vendor/bower/spin.js/spin.js",
		"vendor/bower/moment/min/moment-with-langs.min.js",
		"vendor/bower/holderjs/holder.js",
		"vendor/bower/jquery-cookie/jquery.cookie.js",
		"vendor/bower/cookieCuttr/jquery.cookiecuttr.js",
		"web/js/include/modernizr.custom.js",
	];

	var jsAdminFiles = [
		"vendor/bower/highcharts-release/highcharts.js",
		"modules/admin/js/include/app.js"
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
					"web/js/app.min.js": "web/js/include/app.js",
					"web/js/vendor.min.js": jsFiles,
					"modules/admin/js/app.min.js": jsAdminFiles
				}
			},
			production: {
				options: {
					compress: {
						warnings: false
					}
				},
				files: {
					"web/js/app.min.js": "web/js/include/app.js",
					"web/js/vendor.min.js": jsFiles,
					"modules/admin/js/app.min.js": jsAdminFiles
				}
			}
		},
		less: {
			development: {
				options: {
					compress: true,
					sourceMap: true,
					sourceMapFilename: "app.min.css.map"
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
					"vendor/bower/bootswatch/lumen/bootstrap.min.css",
					"vendor/bower/select2/select2.css",
					"vendor/bower/select2-bootstrap-css/select2-bootstrap.css",
					"web/css/app.min.css"
				],
				dest: "web/css/app.min.css"
			}
		},
		watch: {
			css: {
				files: [
					"web/less/*.less",
					"!web/less/*.source.less",
					"vendor/bower/**/*.less",
					"vendor/bower/**/*.css"
				],
				tasks: [
					"less:development",
					"concat"
				]
			},
			js: {
				files: [
					"web/js/include/**/*.js",
					"modules/admin/js/include/**/*.js",
					"vendor/bower/**/*.js"
				],
				tasks: [
					"uglify:development"
				]
			}
		},
		copy: {
			select_png: {
				expand: true,
				cwd: "vendor/bower/select2/",
				src: "*.png",
				dest: "web/css"
			},
			select_gif: {
				expand: true,
				cwd: "vendor/bower/select2/",
				src: "*.gif",
				dest: "web/css"
			},
			fonts: {
				expand: true,
				cwd: "vendor/bower/bootstrap/fonts/",
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

	grunt.registerTask("build", ["uglify:development", "less:development", "concat", "copy"]);
	grunt.registerTask("build_production", ["uglify:production", "less:production", "concat", "copy"]);

	grunt.registerTask("default", ["build"]);

};
