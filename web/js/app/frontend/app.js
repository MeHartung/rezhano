/*
 * Copyright (c) 2017. Denis N. Ragozin <dragozin@accurateweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */


requirejs.config({
  "baseUrl": "/js/app/frontend",
  waitSeconds: 600,
  "paths": {
    "underscore": "/js/vendor/underscore/underscore",
    "backbone": "/js/vendor/backbone/backbone",
    "backbone-forms": "/js/vendor/backbone-forms/backbone-forms",
    "jquery": "/js/vendor/jquery/jquery-3.2.1",
    "jquery.daterange": "/js/vendor/jquery.daterange/jquery.daterange",
    "jquery-ui": "/js/vendor/jquery-ui-1.12.1/ui",
    "jquery-ui.custom": "/js/vendor/jquery-ui-1.12.1.custom/jquery-ui",
    "jquery-ellipsis": "/js/vendor/jquery.ellipsis",
    "moment": "/js/vendor/momentjs/moment",
    "jquery-validate": "/js/vendor/jquery-validate/jquery-validate",
    //"jquery-mask": "vendor/inputmask/jquery.inputmask",
    "slick": "/js/vendor/slick/slick",
    "jquery-checkbox": "/js/vendor/jquery.ui.checkbox",
    "prettyphoto": "/js/vendor/prettyphoto/js/jquery.prettyPhoto",
    "cleave": "/js/vendor/cleave/cleave",
    "cleave-phone": "/js/vendor/cleave/cleave-phone.ru",
    "sinon": "/js/vendor/sinon/sinon"
  },
  map: {
    "jquery-checkbox": {
      "jquery-ui": "jquery-ui/widget"
    }
  },
  "shim": {
    "backbone": {"deps": ["underscore"], "exports": "Backbone"},
    "underscore": {"exports": "_"},
    "prettyphoto": { "deps": ["jquery"] },
    "sinon" : {"exports": 'sinon'}
  }
});

require([
  'backbone',
  'router/router'
], function (Backbone, Router) {

  var router = new Router();

  Backbone.history.start({
    hashChange: false,
    pushState: true
  });


});