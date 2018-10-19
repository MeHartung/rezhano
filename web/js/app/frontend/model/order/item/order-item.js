/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone'),
      Product = require('model/catalog/product/product');
  
  return Backbone.Model.extend({
    defaults: {
      purchasable_id: null,
      model: null,
      title: null,
      quantity: 1,
      price: null
    },
    initialize: function(){
      /**
       * Хранит ссылку на добавляемый в корзину товар
       * 
       * Не устанавливайте значение этой переменной напрямую. Вместо этого используйте
       * setPurchasable
       */
      this._purchasable = null;
      this.xhr = null;
      
      if (this.has('purchasable')){
        if (this.get('model') == 'product'){
          this.setPurchasable(new Product(this.get('purchasable')));
        }
        this.unset('purchasable', true);
      }
    },
    /**
     * 
     * @param {Backbone.Model} purchasable
     * @returns {undefined}
     */        
    setPurchasable: function(purchasable){
      this._purchasable = purchasable;
      
      this.set({
        purchasable_id: null !== purchasable ? purchasable.get('id') : null,
        model: null !== purchasable ? purchasable.get('model') : null,
        price: null !== purchasable ? purchasable.get('price') : null,
        stock: null !== purchasable ? purchasable.get('stock') : null,
        title: null !== purchasable ? (purchasable.get('product') ? purchasable.get('product').get('title') : purchasable.get('title')) : null,
        url: null !== purchasable ? (purchasable.get('product') ? purchasable.get('product').get('url') : purchasable.get('url')) : null
      });
    },
    getPurchasable: function(){
      return this._purchasable;
    },
    cost: function(){
      return this.get('price')*this.get('quantity');
    },
    getProduct: function(){
      var product;
      
      switch (this.get('model')){
        case 'product': {
          product = this.getPurchasable();
          break;
        }
        case 'sku': {
          var sku = this.getPurchasable();
          if (sku) {
            product = sku.get('product');
          }
          break;
        }
      }
      
      return product;
    },
    clone: function(){
      var clone = Backbone.Model.prototype.clone.apply(this, arguments);

      clone.setPurchasable(this._purchasable);

      return clone;
    },
  });
});

