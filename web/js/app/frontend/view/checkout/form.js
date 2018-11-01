/*
 * @author Alexander Grinevich <agrinevich at accurateweb.ru>
 */

/**
 * Форма оформления заказа
 */
define(function(require){
  var Backbone = require('backbone'),
      ShippingPanel = require('view/checkout/shipping/delivery-method-panel');

  require('jquery-validate');
  require('vendor/inputmask/jquery.inputmask');

  return Backbone.View.extend({
    initialize: function (options) {

      this.shippingMethodsCollection = new Backbone.Collection(this.model.get('shipping_methods'));

      this.shippingPanel = new ShippingPanel({
        collection: this.shippingMethodsCollection
      });

      this.listenTo(this.shippingPanel, 'shippingMethodChange', this.onShippingMethodChange);

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
        },
        'tos-agreement': {
          required: true
        }
      });
      this.listenTo(this.shippingPanel, 'disableAddressValidation', this.disableAddressValidation);
      this.listenTo(this.shippingPanel, 'enableAddressValidation', this.enableAddressValidation);
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
            required : '<ul class="error-list"><li>Представьтесь, пожалуйста</li></ul>'
          },
          email: {
            required : '<ul class="error-list"><li>Укажите Ваш электронный адрес</li></ul>',
            pattern : '<ul class="error-list"><li>Введен неверный адрес электронной почты</li></ul>'
          },
          phone: {
            required: '<ul class="error-list"><li>Укажите Ваш телефон</li></ul>',
            pattern: '<ul class="error-list"><li>Введен неверный номер телефона</li></ul>'
          },
          'address': {
            conditional: '<ul class="error-list"><li>Укажите адрес доставки</li></ul>'
          },
          payment: {
            required : '<ul class="error-list"><li>Выберите способ оплаты</li></ul>'
          },
          delivery: {
            required : '<ul class="error-list"><li>Выберите способ доставки</li></ul>'
          },
          'personal-agreement': {
            required: '<ul class="error-list"><li>Чтобы оформить заказ, вы должны согласиться с политикой компании условиями сотрудничества</li></ul>'
          },
          'tos-agreement': {
            required: '<ul class="error-list"><li>Чтобы оформить заказ, вы должны согласиться с обработкой персональных данных</li></ul>'
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
    onShippingMethodChange: function (shipping_method_id) {
      this.model.set('shipping_method_id', shipping_method_id);
      this.model.save();
    }
  })
});