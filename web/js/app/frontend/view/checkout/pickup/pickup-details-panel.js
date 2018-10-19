/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$
 */
define(function(require){
  var Backbone = require('backbone'),
      PickupPointView = require('view/cart/checkout/pickup/pickup-point');

  require('acommerce/view/lib/collection-view');
  
  var PaymentPanelView = Backbone.CollectionView.extend({
    container: null,
    itemView: PickupPointView,    
    tagName: 'ul',    
    className: 'radio-list',
    
    _createItemView: function(item){
      
      var view = Backbone.CollectionView.prototype._createItemView.apply(this, arguments);
      this.listenTo(view, 'item:selected', this.onItemSelect, this);
      
      return view;
    },
            
    
    onItemSelect: function(){
      this.model.set('pickup_point_id', this.$el.find('input:checked').val());
    }

  });
  
  return PaymentPanelView;
});

