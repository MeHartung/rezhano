/**
 * Created by Денис on 09.06.2017.
 */
define(function(require){
  var ListItemView = require('view/base/list-item-view'),
      QuantityWidget = require('view/cart/widget/quantity-widget');

  require('jquery-ui/widgets/dialog');

  var template = _.template('\
  <div class="cards-container__item">\n' +
'   <a href="<%= product_url %>" class="cards-container__item-image" style="background: url(\'<%= product_image %>\') center no-repeat; background-size: cover"></a>\n' +
'   <div class="cards-container__item-info">\n' +
'     <a href="<%= product_url %>" class="item-info__title"><%= product_name %></a>\n' +
'     <span class="item-info__title-aside">Артикул –&nbsp;<%= product_sku %></span>\n' +
'   </div>\n' +
' </div>' +
' <div class="quantity-wrap">\n' +
'                    <a class="quantity-control quantity-control__down"></a>\n' +
'                    <input class="quantity-control__input" value="<%= quantity %>" type="text">\n' +
'                    <a class="quantity-control quantity-control__up"></a>\n' +
'                    <span class="quantity-balance">всего <%= product_stock %> шт.</span>\n' +
'  </div>\n' +
'  <div class="cards-container__location"><%= city %></div>\
   <div class="cards-container__price"><%= price.toCurrencyString() %></div>\
   <div class="cards-container__controls">\n' +
'    <a class="button button-remove-from-favorites"></a>\n' +
'  </div>\n' +
'<div class="cards-container__amount-total">\n' +
      '                    <div class="total-value">\n' +
      '                      Сумма:\n' +
      '                      <span class="total-value__payment"><% if (cost) { %><%= cost.toCurrencyString() %><% } %></span>\n' +
      '                    </div>\n' +
      '                  </div>');

  return ListItemView.extend({
    //tagName: 'tr',
    className: 'cards-container__wrap',
    events: {
      'click .button-remove-from-favorites': 'onDeleteButtonClick'
    },
    initialize: function(){
      ListItemView.prototype.initialize.apply(this, arguments);

      this.quantityWidget = new QuantityWidget({
        model: this.model,
        min: 0,
        max: this.model.get('product').available_stock
      })

      this.previousQuantity = this.model.get('quantity');
      this.listenTo(this.model, 'change:quantity', this.onQuantityChanged, this);
    },
    render: function(){
      this.quantityWidget.undelegateEvents();
      this.$el.html(template({
        name: this.model.get('name'),
        quantity: this.model.get('quantity'),
        price: Number(this.model.get('price')),
        cost: Number( this.model.get('cost')),
        product_image: this.model.get('product').preview_image ? this.model.get('product').preview_image : '/images/no_photo.png',
        product_name: this.model.get('product').name,
        product_sku: this.model.get('product').sku,
        product_url: this.model.get('product').url,
        city: this.model.get('warehouse')?this.model.get('warehouse').cityName:'',
        product_stock: this.model.get('product').available_stock
      }));


      this.quantityWidget.setElement(this.$('.quantity-wrap'));
      this.quantityWidget.render();

      return this;
    },
    onDeleteButtonClick: function(e){
      e.preventDefault();

      this.model.destroy();

      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({
        'event': 'removeFromCart',
        ecommerce: {
          remove: {
            products: [this.model.toGaJson()]
          }
        }
      });
    },
    onQuantityChanged: function(){
      this.$('.total-value__payment').html(this.model.cost().toCurrencyString());

      var currentQuantity = this.model.get('quantity');

      if (currentQuantity > this.previousQuantity) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
          'event': 'addToCart',
          ecommerce: {
            currencyCode: 'RUB',
            add: {
              products: [this.model.toGaJson({
                quantity: currentQuantity - this.previousQuantity
              })]
            }
          }
        });
      }

      if (currentQuantity < this.previousQuantity) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
          'event': 'removeFromCart',
          ecommerce: {
            remove: {
              products: [this.model.toGaJson({
                quantity: this.previousQuantity - currentQuantity
              })]
            }
          }
        });
      }

        if (currentQuantity == 0) {
          $('body').append('<div class="ui-widget-overlay" style="z-index: 1001; display: block;"></div>');
          var __self = this;
          var previousQuantity = this.quantityWidget.previousValue;
          var dlg = $('<div></div>').dialog({
            autoOpen: true,
            title: 'Удаление товара',
            resizable: false,
            close: function() {
              __self.model.set({ quantity: previousQuantity });
              dlg.dialog('destroy');
              $('.ui-widget-overlay').detach();
            },
            buttons: {
              'Да': function(){
                //Удалить товар
                __self.model.destroy();
                dlg.dialog('destroy');
                $('.ui-widget-overlay').detach();
              },
              'Нет': function(){
                //Оставить 1 товар
                __self.model.set({ quantity: 1 });
                dlg.dialog('destroy');
                $('.ui-widget-overlay').detach();
              }
            }
          }).html('<p><i class="ui-icon ui-icon-alert"></i>Вы действительно хотите удалить товар из корзины?</p>');
        } else {
          this.model.save(null, {
            silent: true
          });
        }
        this.previousQuantity = currentQuantity;
      }
  })
})