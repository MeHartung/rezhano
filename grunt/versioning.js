module.exports = {
  options: {
    cwd: 'web',
    output: 'php',
    outputConfigDir: 'app/config'
  },
  dist: {
    files: [{
      assets: [{
        dest: 'web/js/frontend.js'
      }],
      dest: 'js',
      key: 'app',
      type: 'js',
      ext: '.js'
    }, { 
      assets: [{
          dest: 'web/css/main.css'
      }],
      dest: 'css',
      key: 'app',
      type: 'css',
      ext: '.css'
    }]
  }
};