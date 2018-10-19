/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$
 */
define(function(require){
  var Backbone = require('backbone'),
      OrderItemCollection = require('model/order/item/order-item-collection');
      
  return Backbone.Model.extend({
    defaults: {
      'subtotal': null,
      'shipping_cost': null,
      'total': null
    },   
    initialize: function() {
      this.updateDeliveryMethodsRequest = null;

      this.items = this.createOrderItemCollection();

      this.initializeCities();

      this.on('change:is_juridical', this.onUserTypeChange, this);
      //this.get('delivery').on('change:recipient_city', this.onChangeCity, this);
     
      //this.get('delivery').on('change:method_id', this.updatePaymentMethods, this);
//      this.get('delivery').on('change:price_segment', this.onChangePriceSegment, this);

//      this.get('delivery').on('change:fias_url', this.onChangeFiasUrl, this);
      
      this.on('change:specregion_pickup_department_id', this.updatePaymentMethods, this);
      //this.setDefaults();
    },
            
    initializeCities: function() {
      this.set({ city:  new Backbone.Collection(this.get('city') || [])}); 
    },

            
    onChangeCity: function() {
      var self = this;
      this.fetch({url: "/cities/fias/" + this.get('delivery').get('recipient_city') + "/confirm" });
    },        
    
    onChangePriceSegment: function() {
      var self = this;
      this.fetch({url: "/cities/presence/" + this.get('delivery').get('price_segment') + "/confirm" });
    },
    /**
     * Обновление данных о методах доставки
     */
    updateDeliveryMethods: function() {

      if (null !== this.updateDeliveryMethodsRequest){
        this.updateDeliveryMethodsRequest.abort();
        this.updateDeliveryMethodsRequest = null;
      }
      var self = this;
        /*
         * Тут стоит таймер потому что по непонятным причинам заказ, оказывающийся в
         * this.order это видимо другой инстанс того же самого заказа. Того же самого.
         * Но другой. И у него старая стоимость.
         */
        setTimeout(function(){
          this.updateDeliveryMethodsRequest = self.get('delivery').get('delivery_methods').fetch({
            url: "/cart/update/methods/delivery",
            data: {
              city: self.get('delivery').get('recipient_city'),
              city_id: self.get('delivery').get('city_id'),
              restful: 1
            }
          });
        }, 100);    
    },
    onChangeFiasUrl: function()
    {
      this.fetch({url: this.get('delivery').get('fias_url') });
    },
    getOrderStatusesHistory: function () {
      if (!this.get('order_statuses_history')){
        var statuses = new Backbone.Collection([]);
        this.set('order_statuses_history', statuses);
        statuses.fetch({
          url: urlPrefix + '/api/cabinet/order/statuses/' + this.get('id'),
        });
      }

      return this.get('order_statuses_history');
    },
    setDefaults: function(){
      this.set({
        shipping_method_id: this.shippingMethods.first()
      });
    },
    onUserTypeChange: function(){
      this.updatePaymentMethods();
    },

    createOrderItemCollection: function(){
      return new OrderItemCollection();
    }
  });
  
  return Order;
});