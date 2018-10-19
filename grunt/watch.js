module.exports = {
	options: {
		livereload: true
	},
	styles: {
		files: ['web/less/*.less'],
		tasks: ['less', 'postcss', 'cssmin']
	}
};