/*
 * @author Alexander Grinevich <agrinevich at accurateweb.ru>
 */

/**
 * Форма оформления заказа
 */
define(function(require){
  var Backbone = require('backbone'),
      ShippingPanel = require('view/checkout/shipping/delivery-method-panel'),
      ShippingMethodCollection = require('model/order/shipping/shipping-method-collection');

  require('jquery-validate');
  require('vendor/inputmask/jquery.inputmask');

  return Backbone.View.extend({
    initialize: function (options) {

      this.shippingMethodsCollection = new ShippingMethodCollection(this.model.get('shipping_methods'));

      this.shippingPanel = new ShippingPanel({
        collection: this.shippingMethodsCollection,
        model: this.model
      });

      this.listenTo(this.shippingPanel, 'shippingMethodChange', this.onShippingMethodChange);
      this.listenTo(this.shippingPanel, 'disableAddressValidation', this.disableAddressValidation);
      this.listenTo(this.shippingPanel, 'enableAddressValidation', this.enableAddressValidation);

      this.listenTo(this.shippingPanel, 'enableShipping', this.enableShipping);
      this.listenTo(this.shippingPanel, 'disableShipping', this.disableShipping);

      this.addressRequired = true;

      $.validateExtend({
        email: {
          required: true,
          pattern: /^.+\@.+\..+$/
        },
        name: {
          required: true
        },
        phone: {
          required: true,
          pattern: /\+7\s\(\d{3}\)\s\d{3}\-\d{2}\-\d{2}/
        },
        delivery: {
          required: true
        },
        payment: {
          required: true
        },
        'personal-agreement': {
          required: true
        }
      });
    },
    render: function () {
      this.shippingPanel.setElement(this.$('.shipping-panel'));
      this.shippingPanel.render();

      this.$('#checkout_customer_phone').inputmask('+7 (999) 999-99-99');
      this.initValidation();
      return this;
    },
    initValidation: function () {
      var self = this;
      this.$el.validateDestroy();
      this.$el.validate({
        sendForm: true,
        onChange: true,
        onBlur: true,
        eachValidField : function() {
          var $this = $(this),
            $parent = $this.parents('.step-item');

          $parent.removeClass('error');
        },
        eachInvalidField : function() {
          var $this = $(this),
            $parent = $this.parents('.step-item');

          $parent.addClass('error');
        },
        description : {
          name: {
            required : '<div>Представьтесь, пожалуйста</div>'
          },
          email: {
            required : '<div>Укажите Ваш электронный адрес</div>',
            pattern : '<div>Введен неверный адрес электронной почты</div>'
          },
          phone: {
            required: '<div>Укажите Ваш телефон</div>',
            pattern: '<div>Введен неверный номер телефона</div>'
          },
          'address': {
            conditional: '<div>Укажите адрес доставки</div>'
          },
          payment: {
            required : '<div>Выберите способ оплаты</div>'
          },
          delivery: {
            required : '<div>Выберите способ доставки</div>'
          },
          'personal-agreement': {
            required: '<div>Чтобы оформить заказ, вы должны согласиться с политикой компании и условиями сотрудничества</div>'
          }
        },
        conditional: {
          'address': function(fieldValue){
            if (!self.addressRequired || !!fieldValue){
              return true;
            }

            return false;
          }
        },
        invalid: function(event, options){
          self.isInvalid = true;

          $("html, body").animate({
            scrollTop: self.$('.step-item.error').offset().top - 100
          }, 500)
        },
        valid: function(event, options){
          self.isInvalid = false;
          self.$('#checkout_submit').attr('disabled', 'disabled');

        }
      });
    },
    disableAddressValidation: function () {
      this.addressRequired = false;
    },
    enableAddressValidation: function () {
      this.addressRequired = true;
    },
    enableShipping: function () {
      this.trigger('enableShipping');
    },
    disableShipping: function () {
      this.trigger('disableShipping');
    }
  })
});