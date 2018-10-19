/**
 * Created by Денис on 09.06.2017.
 */
define(function(require){
  var $ = require('jquery'),
      Order = require('model/order/order'),
      ShippingMethodCollection = require('model/order/shipping/shipping-method-collection'),
      PaymentMethodCollection = require('model/order/payment/method/payment-method-collection'),
      CommonView = require('view/common/common-view'),
      ShippingMethodPanelView  = require('view/checkout/shipping/delivery-method-panel'),
      PaymentPanelView  = require('view/checkout/payment/payment-panel'),
      TotalsPanelView = require('view/checkout/checkout-totals-panel'),
      AgreementDialogView = require('view/checkout/agreement/agreement-dialog-view');

  require('vendor/inputmask/jquery.inputmask');

  return CommonView.extend({
    events: {
      'change #checkout_shipping_post_code': 'onPostcodeFieldChange',
      'click .tos-agreement-link': 'onAgreementLinkClick'
    },
    initialize: function(options) {
      var self = this;
      this.updatePaymentMethodsRequest = null;
      this.updateShippingMethodsRequest = null;
      this.agreementDialog = null;

      this.order = options.cart;

      options.cartWidget = false;

      CommonView.prototype.initialize.apply(this, arguments);

      this.shippingMethods = new ShippingMethodCollection();

      this.listenTo(this.order, 'change:shipping_method_id', this.onShippingMethodChanged);
      this.listenTo(this.order, 'change:payment_method_id', this.onPaymentMethodChanged);
      this.listenTo(this.order, 'change:postcode', this.onPostcodeChange);

      this.paymentMethods = new PaymentMethodCollection();


      this.deliveryMethodPanelView = new ShippingMethodPanelView({
          model: this.order,
          collection: this.shippingMethods,
          loader: $('.shipping-ajax-loader')
      });

      this.paymentPanelView = new PaymentPanelView({
          model: this.order,
          collection: this.paymentMethods,
          loader: '.payment-ajax-loader'
      });

      this.totalsPanelView = new TotalsPanelView({
        model: this.order
      });

      this.shippingMethods.fetch();
      this.paymentMethods.fetch();
      this.order.items.fetch({success: function () {
        self.onCartItemFetch();
      }});
    },
    render: function(){
      CommonView.prototype.render.apply(this, arguments);

      this.cartItemListView.setElement(this.$('#basket_container table'));

      this.deliveryMethodPanelView.setElement(this.$('#ajaxshipping'));
      this.deliveryMethodPanelView.render();

      this.paymentPanelView.setElement(this.$('#payment_html'));
      this.paymentPanelView.render();


      this.$('#checkout_customer_phone').inputmask('+7 (999) 999-99-99');

      this.initializeCityAutocomplete();

      return this;
    },
    /**
     * Обновление данных о методах оплаты
     */
    updatePaymentMethods: function() {

      if (null !== this.updatePaymentMethodsRequest){
        this.updatePaymentMethodsRequest.abort();
        this.updatePaymentMethodsRequest = null;
      }
      var self = this;
      self.updatePaymentMethodsRequest = self.paymentMethods.fetch({
        data: {
          city: self.order.get('recipient_city'),
          city_id: self.order.get('city_id'),
          shipping_method_id: self.order.get('shipping_method_id')
        }
      });
    },
    onShippingMethodChanged: function(){
      this.updatePaymentMethods();
    },
    updateShippingMethods: function(){
      if (null !== this.updateShippingMethodsRequest){
        this.updateShippingMethodsRequest.abort();
        this.updateShippingMethodsRequest = null;
      }

      this.updateShippingMethodsRequest = this.shippingMethods.fetch({
        data: {
          postcode: this.order.get('postcode')
        }
      });
    },
    onPostcodeChange: function(){
      this.updateShippingMethods();
    },
    initializeCityAutocomplete: function(){
      //подтягивание города
      var inde='';
      var i_inde=0;
      var n_mas = 200;
      var new_index_i=0;
      var mas = [];
      var lastFocused;
      var lastFocusedId;

      this.$("#checkout_shipping_city_name").autocomplete({
        source : function(request, response) {
          jQuery.ajax({
            url : "//api.cdek.ru/city/getListByTerm/jsonp.php?callback=?",
            dataType : "jsonp",
            data : {
              q : function() {
                return jQuery("#checkout_shipping_city_name").val()
              },
              name_startsWith : function() {
                return jQuery("#checkout_shipping_city_name").val()
              }
            },
            success : function(data) {
              response(jQuery.map(data.geonames, function(item) {
                try {
                  if (item.postCodeArray[0]!=0 && item.postCodeArray[0]!='undefined' && item.postCodeArray[0]!=null)	{
                    inde= item.postCodeArray[0];

                    mas[new_index_i] = [];
                    mas[new_index_i][0] = new_index_i;
                    mas[new_index_i][1] = item.id;
                    mas[new_index_i][2] = item.postCodeArray[0];
                    new_index_i++;

                    i_inde++;
                  }

                } catch (err) {
                  // обработка ошибки
                }

                return {
                  label : item.name,
                  value : item.name,
                  id : item.id
                }
              }));
            }
          });
        },
        minLength : 1,
        autoFocus: true,
        focus: function(e, ui) {
          lastFocused = ui.item.value;
          lastFocusedId = ui.item.id;
        },
        close: function (e, ui) {
          for (var i = 0; i < mas.length; i++){
            if (lastFocusedId==mas[i][1]){
              $('#checkout_shipping_post_code').val(mas[i][2]);
              $('#checkout_shipping_post_code').trigger('change');
            }
          }
          $(this).val(lastFocused);
          $(this).trigger('change');
        },
        select : function(event, ui) {
          //пробегаемся по массиву, чтобы вывести индекс в поле
          for (var i = 0; i < mas.length; i++){
            if (ui.item.id==mas[i][1]){
              lastFocused = ui.item.value;
              $('#checkout_shipping_post_code').val(mas[i][2]);
              $('#checkout_shipping_post_code').trigger('change');
            }
          }
        }
      });
    },
    onPostcodeFieldChange: function(e){
      this.order.set('postcode', $(e.currentTarget).val());
    },
    onAgreementLinkClick: function(e){
      e.preventDefault();

      if (null === this.agreementDialog){
        this.agreementDialog = new AgreementDialogView();
        this.agreementDialog.render().$el.appendTo($('body'));
      }

      this.agreementDialog.open();
    },
    onCartItemFetch: function () {
      var gaProducts = [];

      this.order.items.each(function (item) {
        gaProducts.push(item.toGaJson());
      });

      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({
        'event': 'checkout',
        ecommerce: {
          checkout: {
            'actionField': { 'step': 1 },
            products: gaProducts
          }
        }
      });
    }
  });
});