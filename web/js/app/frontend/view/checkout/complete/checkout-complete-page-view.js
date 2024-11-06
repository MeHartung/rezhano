/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone');
  
  var template = _.template(require('templates/checkout/complete/checkout-complete-page'));
  
  return Backbone.View.extend({
    render: function(){
      this.$el.html(template({
        doc_no: this.model.get('doc_no'),
        cost: this.model.get('cost'),
        cz: this.model.get('cz')
      }));
    }
  });
});

