/**
 * Created by Денис on 31.05.2017.
 */
define(function(require){
  var Backbone = require('backbone');

  return Backbone.View.extend({
    className: 'filter-controll',
    initialize: function(options){
      this.id = options.id;
      this.schema = options.schema;

      this.isChanging = false;

      this.listenTo(this.model, 'change:value', this.onValueChange);
      this.listenTo(this.model, 'change:state', this.onStateChange);
    },
    render: function(){
      return this;
    },
    reset: function(){
      this.model.set('value', null);
    },
    generateName: function(){
      return this.model.get('name');
    },
    generateId: function(){
      return 'f_'+this.id;
    },
    onValueChange: function(){
      if (!this.isChanging){
        this.render();
      }
    },
    onStateChange: function(){
      this.render();
    }
  });
});