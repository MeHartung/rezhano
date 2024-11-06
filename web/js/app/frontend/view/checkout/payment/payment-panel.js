/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$
 */
define(function(require){
  var Backbone = require('backbone'),
      ListView = require('view/base/list-view'),
      PaymentChoiceView = require('view/checkout/payment/payment-choice');

  return ListView.extend({
    container: null,
    itemView: PaymentChoiceView,    
    tagName: 'ul',    
    className: 'radio-list',
    
    
    initialize: function(options) {
      this.options = options;
      
      this.collection.on('fetch:start', this.showLoader, this);
      this.collection.on('fetch:end', this.onCollectionFetchEnd, this);

      this.listenTo(this.collection, 'change:fee', this.onItemChangeFee);

      ListView.prototype.initialize.apply(this, arguments);
    },
    
    showLoader: function() {
      var loader = $(this.options.loader);
      
      if(loader) {
        loader.show();
      }
      
    },
    onCollectionFetchEnd: function(){
      this.hideLoader();
      var selectedPaymentMethodId = this.model.get('payment_method_id'),
          model;
      if (selectedPaymentMethodId) {
          if (model = this.collection.get(selectedPaymentMethodId)) {
              model.set('active', true);
          } else {
            this.model.set({
              payment_method_id: null,
              fee: null
            });
          }
      } else {
        /*
         *  Выбранный по умолчанию способ оплаты будет выбираться каждый раз при появлении. Нам же нужно,
         *  чтобы в случае если пользователь поменял способ доставки так, что выбранный способ оплаты стал недоступен,
         *  после выбора другого способа доставки, чтобы он явно снова выбрал способ оплаты
         */
        this.collection.each(function(model){
          model.set('active', false);
        })
      }
    },
    hideLoader: function() {
      var loader = $(this.options.loader);
      
      if(loader) {
        loader.hide();
      }  
    },    
    _createItemView: function(item){
      var view = ListView.prototype._createItemView.apply(this, arguments);
      this.listenTo(view, 'item:selected', this.onItemSelect, this);
      
      return view;
    },
    onItemSelect: function(){
      var id = this.$el.find('input:checked').val(),
          model = this.collection.get(id);

      if (model){
        this.model.set({
          payment_method_id: model.get('id'),
          fee: model.get('fee')
        });
      } else {
        this.model.set({
          payment_method_id: null,
          fee: null
        });
      }
    },
    onItemChangeFee: function(item){
      if (item.get('id') === this.model.get('payment_method_id')){
        this.model.set('fee', item.get('fee'));
      }
    }

  });
});

