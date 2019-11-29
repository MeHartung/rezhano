module.exports = {
  'frontend': {
    options: {
      waitSeconds: 600,
      baseUrl: "web/js/app/frontend",
      "paths": {
        "underscore": "../../../js/vendor/underscore/underscore",
        "backbone": "../../../js/vendor/backbone/backbone",
        "backbone-forms": "../../../js/vendor/backbone-forms/backbone-forms",
        "jquery": "../../../js/vendor/jquery/jquery-3.2.1",
        "jquery.daterange": "../../../js/vendor/jquery.daterange/jquery.daterange",
        "jquery-ui": "../../../js/vendor/jquery-ui-1.12.1/ui",
        "jquery-ui.custom": "../../../js/vendor/jquery-ui-1.12.1.custom/jquery-ui",
        "jquery-ellipsis": "../../../js/vendor/jquery.ellipsis",
        "jquery.mousewheel": "../../../js/vendor/jquery.mousewheel",
        "moment": "../../../js/vendor/momentjs/moment",
        "jquery-validate": "../../../js/vendor/jquery-validate/jquery-validate",
        "jquery-mask": "../../../js/vendor/jquery.mask/jquery.mask",
        "slick": "../../../js/vendor/slick/slick",
        "jscrollpane": "../../../js/vendor/jquery.jscrollpane.min",
        "jquery-checkbox": "../../../js/vendor/jquery.ui.checkbox",
        "prettyphoto": "../../../js/vendor/prettyphoto/js/jquery.prettyPhoto",
        "sinon": "../../../js/vendor/sinon/sinon",
        "main": "../../../js/main",
        'current-device': '../../../js/vendor/current-device.min',
        // "ymaps": "https://api-maps.yandex.ru/2.1/?lang=ru_RU"
        "ymaps": "https://api-maps.yandex.ru/2.0-stable/?apikey="
      },
      "map": {
          "jquery-checkbox": {
              "jquery-ui": "jquery-ui/widget"
          }
      },
      "shim": {
        "backbone": {"deps": ["underscore"], "exports": "Backbone"},
        "underscore": {"exports": "_"},
        "prettyphoto": { "deps": ["jquery"] }
      },
      name: "app",
      out: "web/js/frontend.js",
      //optimize: "none",
      preserveLicenseComments: false
    }
  }
};
