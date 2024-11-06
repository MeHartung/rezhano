module.exports = {

  options: {
    processors: [
      require('autoprefixer')()
    ]
  },
  dist: {
    src: "web/css/src/main.css",
    dest: "web/css/main.css"
  }
};
