/**
 * @author Max D. Selezenev <selezenev at artsofte.ru>
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
define(function(require){
  var Backbone     = require('backbone'),
      app         = require('core/app'),
      DeliveryMethodPanelView  = require('view/cart/checkout/delivery/delivery-method-panel'),
      PaymentPanelView  = require('view/cart/checkout/payment/payment-panel'),
      DeliveryCityPanelView  = require('view/cart/checkout/city/delivery-city-panel'),
      //LegalEntityPanelView  = require('view/cart/checkout/legal/legal-entity-panel'),
      CheckoutErrorDialogView = require('view/cart/checkout/checkout-error-dialog-view'),
      actionJournal = require('model/journal/action-journal');

  var totalCostTemplate = _.template('\
						Итоговая стоимость заказа вместе с доставкой и скидками:<br>\n\
						<%= cost.toCurrencyString() %>\n\
                ');
  var showDetailsFileTemplete = _.template('\
     <span class="green"><%= fileName %>  прикреплен</span><br/> <a id="details-file-initialize" href="#" class="dashed"> выбрать другую карточку предприятия </a>');

  return Backbone.View.extend({
    el: '.ordering',
    events: {
      'change #order_is_juridical' : 'onUserTypeChange',
      'change #order_recipient_city' : 'onRecipientCityChange',
      'click #personal-agreement-initialize' : 'showAgreement',
      'click #details-file-initialize' : 'showDetailsFile',
      'change #order_personal_agreement': 'checkOffer',
      'change input, textarea': 'fieldChange',
      'submit form': 'onFormSubmit',
      
      'keyup input[data-describedby]': 'onValidatedFieldKeyup',
      
      'change .delivery-method input': 'onShippingChoiceChange',
      'change .payment-method input': 'onPaymentChoiceChange',
      'change #order_add_comment': 'onOrderCommentToggleChange',
      'change #order_autoconfirm': 'onDoNotCallMeCheckboxChange',
      'change #order_recipient_phone': 'onOrderRecipientPhoneChange',
      'change #order_recipient_email': 'onOrderRecipientEmailChange',
      'keyup #order_recipient_phone': 'onOrderRecipientPhoneChange',
      'keyup #order_recipient_email': 'onOrderRecipientEmailChange',
      
      'click .online-payment-legal-link': 'onOnlinePaymentLegalLinkClick'
    },
    constructor : function ( attributes, options ) {
      Backbone.View.apply( this, arguments );
//      if (true !== ObjectCache.CurrentUser[0].admin) {
        this.fillForm();
//      }
      
      this.updateAddressFieldFromPickupChoice();   
      this.updateSpecregionDepartmentId();
    },
    initialize: function(options) {
      this.options = options;
      //var deliveryModel = this.model.get('delivery');
      
      this.model.on('change:is_juridical', this.toggleDetails, this);
      
      this.listenTo(this.model, 'change:shipping_method_id', this.onDeliveryMethodChange, this);
      //this.listenTo(deliveryModel, 'change:recipient_city', this.onCityChange, this);
      
//      this.model.get('delivery').on('change:method_id', this.onDeliveryMethodChange, this);
//      this.model.get('delivery').on('change:city_id', this.onCityChange, this);      
      
      this.listenTo(this.model.get('payment').get('payment_methods'), 'remove reset', this.onPaymentMethodRemoveOrReset);
      
      this.model.on('change:cost', this.renderCost, this);
      this.agreement = $('.layer.personal-agreement');
      this.isInvalid = true;
      
      this.anotherCityPanel = $('.another-city-panel', this.$el);

      this.juridicalDetails = $('.juridical-user-details', this.$el);
      
      this.legalPanelView = null;
      this.deliveryCityPanelView = null;
      this.deliveryMethodPanelView = null;
      this.paymentPanelView = null;
      this.isFillingForm = false;
      
      this.addressRequired = false;
      
      this.lastTransactionId = null;
      
      if (this.$('#order_is_juridical').length){
        var isJuridical = $('#order_is_juridical', this.$el).is(':checked');

        this.model.set('is_juridical', isJuridical);
      }
      
      this.offerCheckbox = $('#order_personal_agreement', this.$el);
      this.orderButton = $('#order_button', this.$el);
      this.checkOffer();
      
      this.$('#order_recipient_name').focus();

      this.deliveryCityPanelView = new DeliveryCityPanelView({
        model: this.options.app.components.currentUser.location,
        el: this.$('.recipient-city'),
        simple: true
      }).render();
      
      this.deliveryMethodPanelView = new DeliveryMethodPanelView({
          model: this.model,
          collection: this.model.shippingMethods  || new Backbone.Collection(),
          el: '.delivery-method',
          app: this.options.app
        });
        
      this.listenTo(this.deliveryMethodPanelView, 'Loaded', this.onDeliveryMethodPanelLoaded);
      //this.deliveryMethodPanelView.render();
      
      this.paymentPanelView = new PaymentPanelView({
          model: this.model,
          collection: this.model.get('payment').get('payment_methods') || new Backbone.Collection(),
          el: '.payment-method ul.radio-list',
          loader: '.payment-method .ajax-loader'
      });

      this.listenTo(this.paymentPanelView, 'update', this.onPaymentPanelViewUpdate, this);
      this.listenTo(this.deliveryMethodPanelView, 'change:tab', this.onDeliveryMethodListTabChange, this);

      this.render();
      
      var self = this;
      setTimeout(function() {
          self.initValidation();
      }, 5000);

      $.validateExtend({
        name: {
          required: true,
          pattern: /^.{3,}$/
        },
        phone: {
          required: true,
          pattern: /\+7\s\(\d{3}\)\s\d{3}\-\d{2}\-\d{2}/
        },
        email: {
          required: false,
          pattern: /^.+\@.+\..+$/
        }
      });


      this.favoriteShippingDestination = app.user.getFavoriteShippingDestination();
      if (this.favoriteShippingDestination && this.favoriteShippingDestination.deparment_id && !app.user.isAdmin()) {
        this.$('input[data-department-id='+this.favoriteShippingDestination.deparment_id+']').trigger("click");
      }



      //this.$('input[name="order[delivery_method_id]"]:first').attr('checked', true).trigger('change');
    },
    initValidation: function(){
      var self = this;
        this.$('form').validate({
            sendForm: true,
            onChange: true,
            onBlur: true,
            eachValidField : function() {
                var $this = $(this),
                    $parent = $this.parent();

                if ($parent.hasClass('radio')){
                    $parent = $parent.parent();
                };

                $parent.removeClass('invalid');
                if ($this.val()) {
                    $parent.addClass('valid');
                }
            },
            eachInvalidField : function() {
                var $this = $(this),
                    $parent = $this.parent();

                if ($parent.hasClass('radio')){
                    $parent = $parent.parent();
                };

                $parent.removeClass('valid').addClass('invalid');
            },
            description : {
                name: {
                    required : '<ul class="error_list"><li>Представьтесь, пожалуйста</li></ul>',
                    pattern : '<ul class="error_list"><li>Пожалуйста, введите хотя бы три символа</li></ul>'
                },
                email: {
                    required : '<ul class="error_list"><li>Укажите Ваш электронный адрес</li></ul>',
                    pattern : '<ul class="error_list"><li>Введен неверный адрес электронной почты</li></ul>'
                },
                phone: {
                    required: '<ul class="error_list"><li>Укажите Ваш телефон</li></ul>',
                    pattern: '<ul class="error_list"><li>Введен неверный номер телефона</li></ul>'
                },
                'company-name': {
                    conditional: '<ul class="error_list"><li>Укажите название компании</li></ul>'
                },
                'address': {
                    conditional: '<ul class="error_list"><li>Укажите адрес доставки</li></ul>'
                },
                'shipping-service-provider-name': {
                    conditional: '<ul class="error_list"><li>Укажите название транспортной компании, которую следует использовать для отправки Вашего заказа</li></ul>'
                },
                'delivery-method': {
                    required: '<ul class="error_list"><li>Выберите способ получения заказа</li></ul>'
                },
                'payment-method': {
                    required: '<ul class="error_list"><li>Выберите способ оплаты</li></ul>'
                }
            },
            conditional: {
                'company-name': function(fieldValue){
                    if (self.model.get('is_juridical') && !fieldValue){
                        return false;
                    }

                    return true;
                },
                'address': function(fieldValue){
                    if (!self.addressRequired || !!fieldValue){
                        return true;
                    }

                    return false;
                },
                'shipping-service-provider-name': function(fieldValue){
                    if (self.model.get('shipping_method_id') == '2cdc5b09-c88c-4698-93dd-50ffebff8364' /*Другая транспортная компания*/ && !fieldValue){
                        return false;
                    }
                    return true;
                }
            },
            invalid: function(event, options){
                var $firstInvalidField = self.$('.invalid:first');
                if ($firstInvalidField.length) {
                    $('body').animate({scrollTop: Math.max($firstInvalidField.offset().top - $(window).height() / 3, 0)}, 'fast');
                }
                self.isInvalid = true;
            },
            valid: function(event, options){
                self.isInvalid = false;
            }
        });
    },
    onDeliveryMethodChange: function() {
      var deliveryMethod = this.model.shippingMethods.findWhere({ id: this.model.get('shipping_method_id')});
      
      if (typeof deliveryMethod === 'undefined' || (deliveryMethod && deliveryMethod.get('options').recipient_address_required === false)){
        this.disableAddressField();
      } else {
        this.enableAddressField();        
      }        
    },
    render: function() {
      //this.onCityChange();
      
      this.deliveryMethodPanelView.render();
      
      
      return this;
    },
 
    showAgreement: function(event)
    {
      
      openLayer(this.agreement);
      event.preventDefault();
    },  

    showDetailsFile: function(event)
    {
        var self = this;
        $('#order_file_details').unbind().click();
        $('#order_file_details').on('change', function() {
            if ($(this).val() != '') {
                var uploadFile = $(this).val();
                var regEx = new RegExp("pdf$|txt$|doc(x)?$|xls(x)?$", "i");
                if (regEx.test(uploadFile)) {
                    self.$el.find('#details-file-remark').html(showDetailsFileTemplete({fileName: uploadFile}));
                } else {
                    openLayer('.error-reg-layer');
                }
            }
        });
        return false;
    },

    onRecipientCityChange: function(event) {
      var recipientCity = $(event.currentTarget).val();
      if(this.model.has('delivery')){
        this.model.get('delivery').set('recipient_city', recipientCity);
      }
      window.location.reload();
    },                   
    onUserTypeChange: function(event)
    {
      var element = event !== undefined ? $(event.currentTarget) : null;
      var isLegalEntity = ( element !== null ? element.is(':checked') : false);
      
      this.model.set('is_juridical', isLegalEntity);
    },            
    toggleDetails: function() {
      var isLegalEntity =  this.model.get('is_juridical');    

      if (isLegalEntity && this.juridicalDetails) {
        this.juridicalDetails.show();
      } else {
        this.juridicalDetails.hide();
      } 
      
      //Сбрасываем состояние валидации для полей "Компания", "Реквизиты"
      if (!isLegalEntity){
        this.$el.find('.juridical-user-details input, .juridical-user-details textarea').trigger('change');
      }      
    },

    onCityChange: function() {
      window.location.reload();
//      var cityId = this.model.get('delivery').get('city_id');
//
//      if ('' !== cityId && cityId !== undefined && cityId !== null) {
//        this.anotherCityPanel.hide();
//      }
//      else {
//        this.anotherCityPanel.show();
//      }
//
//      this.onDeliveryMethodChange();
    },

    renderCost: function() {
      this.$el.find('.order-total-price').html(totalCostTemplate({cost: this.model.get('cost')})).show();
    },
    checkOffer: function()
    {
      //this.offerCheckbox.prop('checked') ? this.enableSubmit() : this.disableSubmit();
    },    
    fieldChange: function (e) {
      var field = e.target;
      
      if (field) {
        var $field = $(field);
//        if (this.validateField($field)) {
          if (!this.isFillingForm){
            this.saveFieldValue($field);
          }
//          $field.removeClass('invalid');
//        } else {
//          $field.addClass('invalid');
//        }
      }
    },    
    fillForm: function() {
      if (localStorage){
          this.isFillingForm = true;

          this.$('#order_recipient_name, #order_recipient_phone, #order_recipient_email,\
#order_is_juridical, #order_company_name, #order_details, #order_add_comment,\
#order_autoconfirm, #order_disable_notifications, #order_comment, #order_shipping_service_provider_name, \
#order_recipient_address, .payment-method input, .delivery-method input').each(function(k, v) {
              var $v = $(v);

              if (localStorage[$v.prop('id')] !== null) {
                  if ($v.prop('type') === 'radio') {
                      if (localStorage[$v.prop('id')] === 'true') {
                          $v.prop('checked', true).trigger('change', { currentTarget: v});
                      }
                  } else if ($v.prop('type') === 'text' || $v.prop("tagName").toLowerCase() == 'textarea') {
                      $v.prop('value', localStorage[$v.prop('id')]);
                      if ($v.val()){
                          $v.trigger('change', { currentTarget: v});
                      }
                  } else if ($v.prop('type') === 'checkbox') {
                      if (localStorage[$v.prop('id')] === 'true') {
                          $v.prop('checked', true).trigger('change', { currentTarget: v});
                      } else {
                          $v.prop('checked', false).trigger('change', { currentTarget: v});
                      }
                  }
              }
          });


          this.isFillingForm = false;
      }


      this.updateAutoconfirmCheckboxActivity();
    },
    /**
     * 
     */
    enableSubmit: function(){
      this.orderButton.removeAttr('disabled');
    },
    disableSubmit: function(){
      this.orderButton.attr('disabled', 'disabled')   
    },
    isValid: function(){
      return this.isValid;
    },
    saveFieldValue: function (field) {
      if (localStorage) {
        if (field.prop('type') == 'radio') {
          var id = field.prop('id'),
            prefix = id.match(/^(order_(delivery|payment)_method_id)/);
          if (prefix && prefix.length > 1) {
            for (var i = 0, len = localStorage.length; i < len; i++) {
              var key = localStorage.key(i);
              if (key && key.indexOf(prefix[1]) >= 0) {
                try {
                  localStorage.removeItem(key);
                  i--;
                } catch (e) {
                  console.log('Unable to remove localStorage key ' + key);
                }
              }
            }
          }

          localStorage[field.prop('id')] = !!field.prop('checked');
        } else if (field.prop('type') == 'text') {
          localStorage[field.prop('id')] = field.prop('value');
        } else if (field.prop('tagName').toLowerCase() == 'textarea') {
          localStorage[field.prop('id')] = field.val();
        } else if (field.prop('type') == 'checkbox') {
          localStorage[field.prop('id')] = !!field.prop('checked');
        }
      }
    },
    validateField: function(field){
      $.post('/cart/checkout/validate', {
        'field': field.attr('name'),
        'value': field.val()
      }, function(){
        
      });
      return false;
    },
    onShippingChoiceChange: function(e){
      var $choice = $(e.currentTarget);
      
      if($choice.is(':checked')) {
        
        if (undefined !== $choice.attr("data-department-id") && !app.user.isAdmin()) {
          this.favoriteStoreId = $choice.attr("data-department-id");
          localStorage["favorite_department_id"] = this.favoriteStoreId;
        }
        localStorage["favorite_delivery_method_id"] = $choice.attr("value");
        this.updateAddressFieldFromPickupChoice($choice);
        this.updateSpecregionDepartmentId($choice);
      }
    },
    updateAddressFieldFromPickupChoice: function($choice){
      var $addressInput = $('#order_recipient_address');

      $choice || ($choice = $('.delivery-method input:checked'));

      if ($choice.length){
        if ($choice.attr('data-address')){
          this.disableAddressField();
          $addressInput.val($choice.attr('data-name') + ' (' + $choice.attr('data-address') + ')');
        } else {
          
          var isCourierShippingChoiceSelected = false;
          
          this.deliveryMethodPanelView.courierShippingChoices.each(function(item){
            if (item.get("id") === $choice.attr('value')) {
              isCourierShippingChoiceSelected = true;
            }
          });
          
          /*
           * ТК (Желдорэкспедиция, Почта России, "Другая транспортная компания", одна из курьерских доставок) были перенесены на вкладку самовывоза, хотя фактически являются "курьерскими" и требуют ввода адреса,
           * поэтому для Почты России включим поле адреса, несмотря на то, что посетитель на вкладке самовывоза
           */
          if ($choice.attr('value') == 'ecc4f177-526e-471f-8f37-5608f1ca86bc' ||
              isCourierShippingChoiceSelected){
            $addressInput.val(localStorage["order_recipient_address"]);
            this.enableAddressField()
          } else {
            $addressInput.val('');
          }       
        }
      }
    },
    updateSpecregionDepartmentId: function($choice){
      var $input = $('#order_specregion_pickup_department_id');
      
      $choice || ($choice = $('.delivery-method input:checked'));
      
      var val = '';
      
      if ($choice.length){
        var id = $choice.attr('data-department-id');
        if (id){
          val = id;
        }
      }
      
      $input.val(val);
      this.model.set('specregion_pickup_department_id', val ? val : null);
    },
    onDeliveryMethodPanelLoaded: function(e) {
      if (this.favoriteShippingDestination) {
        var saved_address = this.favoriteShippingDestination.address;
        var isSpecregion = false;

        this.$('.delivery-method').find('input').each(function (index, element) {
          var data_name = $(element).attr('data-name');
          var data_address = $(element).attr('data-address');
          var address = data_name + ' (' + data_address + ')';
          if (null !== data_address) {
            if (saved_address === address && !app.user.isAdmin()) {
              $(element).trigger('click');
              isSpecregion = true;
            }
          }
        });

        if (!isSpecregion) {
          $('input[value=' + this.favoriteShippingDestination.shipping_method_id + ']').trigger('click');
          $('#order_recipient_address').val(saved_address);
        }
      }

      if (!this.$('.delivery-method input:checked').length){
        this.$('.delivery-method input').each(function(){
          var $input = $(this);
          if (localStorage[$input.prop('id')] === 'true') {
            $input.prop('checked', true).trigger('change', { currentTarget: this });
          }
        })
      }

      this.initValidation();
    },
    onValidatedFieldKeyup: function(e){
      var $input = $(e.currentTarget),
          isEmpty = !$input.val().length;
      if (undefined !== $input.data('buffer')){
        //Это маска телефона. Костыльно, но ничего не поделаешь.
        isEmpty = $input.val() == '+7 (___) ___-__-__';
      }
      if (isEmpty){
        $input.parent().removeClass('invalid').removeClass('valid');
        $input.parent().find('.error_list').remove();
      }
    },
    onDeliveryMethodListTabChange: function(event, options){
      var $addressInput = $('#order_recipient_address');
      if (options.tabId == 'delivery-tab-pickup' || this.$('#'+options.tabId).find('input').length == 0){
        this.disableAddressField();
        this.updateAddressFieldFromPickupChoice();
      } else {
        this.enableAddressField();
        try {
          if (!localStorage['order_recipient_address']) {
            $addressInput.val('');
          } else {
            $addressInput.val(localStorage['order_recipient_address']);
          }
        } catch (e) {
          $addressInput.val('');
        }

      }
    },
    enableAddressField: function(){
      this.$('.recipient_address').show();
      this.addressRequired = true;
    },
    disableAddressField: function(){
      this.$('.recipient_address').hide();
      this.addressRequired = false;
    },
    onOrderCommentToggleChange: function(e){
      var $checkbox = $(e.target);
      if ($checkbox.is(':checked')){
        $('#order-comment-field-row').show();
      } else {
        $('#order-comment-field-row').hide();
        $('#order_comment').val('');
      }
    },
    onDoNotCallMeCheckboxChange: function(e){
      var $checkbox = $(e.target);
      if ($checkbox.is(':checked')){
        $('#autoconfirm-clickzone').show();
      } else {
        $('#autoconfirm-clickzone').hide();
      }      
    },
    onShippingChoiceListToggleClick: function(e){
      e.preventDefault();
      var $this = $(e.target),
          $container = $(e.target).parents('.delivery-tab:first');
      var $extra = $container.find('.shipping-choice-extra');      
      if ($extra.is(':visible')){        
        $extra.slideUp(function(){
          $this.text('Показать еще '+$this.attr('data-count'));
        });    
      } else {        
        $this.text('Свернуть');
        $extra.slideDown();
      }
    },
    
    submit: function(){

      var self = this,
          $form = this.$('form');
      var formattedDate = this.getFormatDate();
      var department_id = 0;

      //Если форма валидна отправим запрос
      if (!this.isInvalid) {
        self.disableSubmit();

        actionJournal.record({
            op: actionJournal.operations.CHECKOUT_ATTEMPT,
            time: formattedDate,
        });
        if (null !== this.favoriteStoreId && !app.user.isAdmin()) {
            localStorage["favorite_department_id"] = this.favoriteStoreId;
        }

        var loader = this.$('.form-buttons .ajax-loader').show();
        $form.ajaxSubmit({
          dataType: 'json',
          timeout: 20000,
          //В IE8 ajaxSubmit не отправляет заголовок X_REQUESTED_WITH в запросе
          data: {
            'X_REQUESTED_WITH': 'XMLHttpRequest'
          },
          error: function(r){
            if (400 === r.status){
              self.reset();

              $form.html(r.responseText);

              self.initialize(self.options);
              self.initializeHelpTooltips();
              self.$('input[type=checkbox]').checkbox();

            } else {
              actionJournal.record({
                  op: actionJournal.operations.CHECKOUT_FAIL,
                  time: formattedDate, 
                  data: { status: r.status }
              });
              actionJournal.send();
              
              // Отправляем событие в Google Analytics

              window.dataLayer = window.dataLayer || [];
              dataLayer.push({ 
                event: 'checkoutError', 
                name: self.$('#order_recipient_name').val(),
                phone: self.$('#order_recipient_phone').val(),
                email: self.$('#order_recipient_email').val(),
                error: r.status + '-' + r.responseText,
                time: (new Date()).toLocaleTimeString()
              });

              (new CheckoutErrorDialogView({
                model: this.model,
                form: self
              })).open();
            }
          },
          success: function(r){
            //С этого момента больше нажимать кнопку "Оформить заказ" нельзя.
            self.disableSubmit();

            //Дополнительная проверка на случай, если одновременно отправятся два запроса, 
            //оба из которых будут успешными. Чтобы оформить новый заказ, посетитель 
            //должен перезагрузить страницу.
            if (null === self.lastTransactionId){
              self.lastTransactionId = r.transaction.transactionId;
              
              window.dataLayer = window.dataLayer || [];
              dataLayer.push($.extend({
                  event: "transaction"
                }, r.transaction
              ));
              dataLayer.push(r.getransaction);
            }

            if (ObjectCache.CurrentUser[0].salesconsult || ObjectCache.CurrentUser[0].admin){
              self.clearSavedValues();  
            }

            actionJournal.clear();

            actionJournal.record({
              op: actionJournal.operations.CHECKOUT_SUCCESS,
              time: formattedDate, 
              data: { doc_no: r.transaction.transactionId }
            });

            //Если указан адрес перенаправления на страницу банка, выполним выполним перенаправление
            if ('undefined' !== typeof r.location){
              setTimeout(function(){
                window.location.href = r.location;
              }, 1000);  
            } else {
              //В противном случае отобразим страницу "Спасибо за Ваш заказ"
              if (!history.pushState) {
                setTimeout(function(){
                  window.location.href = app.url('/orders/'+r.transaction.transactionId+'/completed');
                }, 1000);  
              } else {
                ObjectCache.CompletedOrderInfo = {
                  doc_no: r.transaction.transactionId,
                  cost: r.cost,
                  cz: r.cz
                };
                $('body,html').animate({
                  scrollTop: 0
                }, 200);
                Backbone.history.navigate(app.url('/orders/'+r.transaction.transactionId+'/completed'), { trigger: true });
              }
            } 
          },
          complete: function(){
            self.enableSubmit();
            loader.hide();
          }
        });
      }
    },
    onFormSubmit: function(e){
      
      e.preventDefault();
      
      this.submit();
      
    },
    /**
     * Отключает все слушатели событий, выгружает все вложенные представления
     */
    reset: function(){
      
    },
    /**
     * Выгружает все вложенные представления и удаляет элемент из DOM
     */
    dispose: function(){
      this.reset();
      this.undelegateEvents();
      this.$el.remove();
    },
    initializeHelpTooltips: function(){
      this.$('.info').each(function(){
        var cloud = $(this).find('.i-layer'),
            icon = $(this).find('.i-icon');

        cloud.hide();

        $(this).on("mouseover", function(){
          cloud.removeClass('info-popup').show(1, function(){
            $(this).addClass('info-popup');
          });

          $(this).css('z-index','6').addClass('active');

          var cloud_right = cloud.offset().left + cloud.width();
          var brouse_width = $(window).width();
          if (cloud_right > brouse_width) {
            cloud.css('left', -cloud.width()).addClass('i-layer-right');
          }
        }).on("mouseleave", function(){ hideInfo(); });
      });
    },
    onOrderRecipientPhoneChange: function(e){
      this.updateAutoconfirmCheckboxActivity();
    },
    onOrderRecipientEmailChange: function(e){
      this.updateAutoconfirmCheckboxActivity();      
    },
    /**
     * Если заказчик не указал почту, и его телефон не начинается на +79.. дизэйблит флажок "не звоните мне"
     * 
     * @returns {undefined}
     */
    updateAutoconfirmCheckboxActivity: function(){
      if (this.$('#order_recipient_email').val()){
        this.$('#order_autoconfirm').checkbox('enable');
        this.$('label[for="order_autoconfirm"]').removeClass('grey');
      } else {
        if (this.$('#order_recipient_phone').val().substring(0, 5) == "+7 (9"){
          this.$('#order_autoconfirm').checkbox('enable');
          this.$('label[for="order_autoconfirm"]').removeClass('grey');
        } else {
          this.$('#order_autoconfirm').checkbox('removeChecked').checkbox('disable');
          this.$('label[for="order_autoconfirm"]').addClass('grey');          
        }        
      }
    },
    getFormatDate: function() {
      var date = new Date();
      var month = (date.getMonth()+1);
      var day = date.getDate();
      var hours = date.getHours();
      var minutes = date.getMinutes();
      var seconds = date.getSeconds();
      
      if (month < 10) {
        month = '0' + month;
      }
      if (day < 10) {
        day = '0' + day;
      }
      if (hours < 10) {
        hours = '0' + hours;
      }
      if (minutes < 10) {
        minutes = '0' + minutes;
      }
      if (seconds < 10) {
        seconds = '0' + seconds;
      }

      return date.getFullYear()+'-'+month+'-'+day+' '+
             hours+':'+minutes+':'+seconds;
    },
    onPaymentChoiceChange: function(e){
      var $input = $(e.currentTarget);
      if ($input.val() == 4){
        this.$('#online-payment-help').show();
      } else {
        this.$('#online-payment-help').hide();
      }
    },
    onOnlinePaymentLegalLinkClick: function(e){
      e.preventDefault();
   
      openLayer($('#online-payment-legal-layer'));      
    },
    onPaymentMethodRemoveOrReset: function(e){
      this.$('#online-payment-help').hide();
    },
    clearSavedValues: function(){
      var keys = ['favorite_department_id', 'order_recipient_name', 'order_recipient_phone', 'order_recipient_email',
                  'order_is_juridical', 'order_company_name', 'order_details', 'order_add_comment',
                  'order_autoconfirm', 'order_disable_notifications', 'order_comment', 'order_shipping_service_provider_name', 
                  'order_recipient_address'];                
      for (var i = 0; i < keys.length; i++) {
        try {
          localStorage.removeItem(keys[i]);
        } catch (e){
          console.log('Unable to remove localStorage key '+key);
        }
      }          
      for(var i = 0, len = localStorage.length; i<len; i++) {
          var key = localStorage.key(i);
          if (key && (key.indexOf('order_delivery_method_id') >= 0 || key.indexOf('order_payment_method_id') >= 0)){
            try {
              localStorage.removeItem(key);
              i--;
            } catch (e) {
              console.log('Unable to remove localStorage key '+key);
            }
          }
      }
    },
    onPaymentPanelViewUpdate: function(){
      this.initValidation();
      this.$('.payment-method input').each(function(){
        var $input = $(this);
        if (localStorage[$input.prop('id')] === 'true') {
          $input.prop('checked', true).trigger('change', { currentTarget: this });
        }
      })
    }
  });
});