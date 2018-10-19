/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var ListView = require('view/base/list-view'),
      CourierShippingChoiceListItem = require('view/checkout/shipping/shipping-choice-list-item')/*,
      HelpIconView = require('view/common/help-icon-view')*/;
  
  return ListView.extend({
    itemView: CourierShippingChoiceListItem,
    container: '.list-container',
    template: _.template(require('templates/checkout/shipping/shipping-choice-list-view')),
    initialize: function(){
      ListView.prototype.initialize.apply(this, arguments);

      // this.costColumnHintView = null;
      // if (ObjectCache.CheckoutFormOptions[0].cost_column_hint) {
      //   this.costColumnHintView = new HelpIconView({
      //     tagName: 'span',
      //     className: 'info field-help',
      //     model: new Backbone.Model({
      //       text: ObjectCache.CheckoutFormOptions[0].cost_column_hint
      //     })
      //   })
      // }
    },
    renderContainer: function(){
      ListView.prototype.renderContainer.apply(this, arguments);

      // if (this.costColumnHintView){
      //   this.costColumnHintView.undelegateEvents();
      //   this.costColumnHintView.render().$el.appendTo(this.$('.cost-column-header'))
      //   this.costColumnHintView.delegateEvents();
      // }
    }
  });
});

