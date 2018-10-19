/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone');
  
  return Backbone.Model.extend({
    defaults: {
      name: '',
      itemcount: 0,
      warning: null,
      active: false,
      enabled: false,
      rel: false,
      loading: false
    },
    validate: function(attrs, options){
      if (attrs.active && !attrs.enabled){
        return 'disabled tab cannot be active';
      }      
    },
    initialize: function(){
      this.on('change:active', this.onActiveChange, this);
      this.on('change:itemcount', this._updateEnabled, this);

      this._updateEnabled();
    },
    onActiveChange: function(){
      if (this.get('active')){
        var self = this;
        if (this.collection){
          _.each(this.collection.where({ active: true }), function(tab){
            if (tab.cid != self.cid){
              tab.set('active', false);
            }
          });
        }
      }
    },
    _updateEnabled: function(){
      this.set({
        enabled: this.get('itemcount') > 0 || this.get('warning')
      });
    }
  });
});

