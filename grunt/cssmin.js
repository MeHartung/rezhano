module.exports = {
	/*target: {
	 files: [{
	 expand: true,
	 cwd: 'dist/css',
	 src: ['*.css', '!*.min.css'],
	 dest: 'dist/css',
	 ext: '.min.css'
	 }]
	 }*/
    options: {
        keepSpecialComments: 0
    },
    site: {
        src: ['web/css/main.css'],
        dest: 'web/css/main.css'
    }
};