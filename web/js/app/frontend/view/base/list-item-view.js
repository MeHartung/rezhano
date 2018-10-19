/*
 * Copyright (c) 2017. Denis N. Ragozin <dragozin@accurateweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

define(function(require){
  var Backbone = require('backbone');

  return Backbone.View.extend({
    initialize: function(){
      this.listenTo(this, 'attach', this.onAttach);
    },
    dispose: function(){
      this.stopListening();
      this.$el.remove();
    },
    onAttach: function(){
      this.render();
    }
  });
})
