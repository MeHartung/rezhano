/**
 * Created by Денис on 06.06.2017.
 */
define(function(require){
  var ModalDialogView = require('view/dialog/base/modal-dialog-view');

  var template = _.template('\
        <div class="layer__close"></div>\
        <div class="layer__container">\
          <div class="layer__title">Товар добавлен в корзину</div>\
          <div class="add-to-cart-layer__wrap">\
              <a href="<%= product_url %>" class="add-to-cart-layer__image" style="background: url(\'<%= preview_image %>\') center no-repeat">\
              </a>\
            <div class="add-to-cart-layer__info">\
              <a href="<%= product_url %>" class="add-to-cart-layer__product-name"><%= name %></a>\
              <div class="add-to-cart-layer__product-value"><%= quantity %> шт. &nbsp;&nbsp; <%= price.toCurrencyString() %></div>\
            </div>\
          </div>\
        </div>\
        <div class="buttons">\
          <a class="button" href="<%= urlPrefix %>/cart">Перейти в корзину</a>\
          <a class="button white continue-shopping" href="#">Продолжить покупки</a>\
        </div>\
<!--<div class="layer__close"></div>\-->\
<!-- <div class="layer__in"> -->\
      <!--<p><%= quantity %> x  <a href="<%= product_url %>"><%= name %></a> добавлен<% if (quantity > 1) {%>ы<% } %> в Вашу корзину.</p> -->\
      <!-- <% if (quantityChanged) { %><% } %> -->\
      <!--<h3>Добавление в корзину</h3>-->\
      <!--<div class="order-wrap__item">-->\
        <!--<img class="order-pic" src="<%= preview_image %>" alt="" >-->\
        <!--<a href="#" class="order-name"><%= name %></a>-->\
        <!--<span class="order-quantity"><%= quantity %> шт.</span>-->\
        <!--<span class="order-price"><%= price.toCurrencyString() %></span>-->\
      <!--</div>-->\
      <!--<a class="button button-grey" href="#">Продолжить покупки</a>-->\
      <!--<a class="button show__cart" href="<%= urlPrefix %>/cart">Показать корзину</a>-->\
      <!--<br style="clear:both">-->\
<!--</div>-->\
');

  return ModalDialogView.extend({
    id: 'facebox',
    className: 'layer add-to-cart-layer',
    template: template,
    events: {
      'click .layer__close': 'onCloseClick',
      'click .continue-shopping': 'onCloseClick',
      'click .button-grey': 'onContinueClick',
      'click .showcart': 'onShowCartClick'
    },
    initialize: function(options){
      this.quantity = options.quantity;

      ModalDialogView.prototype.initialize.apply(this, arguments);
    },
    onCloseClick: function(e){
      e.preventDefault();

      this.close();
    },
    onContinueClick: function(e){
      e.preventDefault();

      this.close();
    },
    onShowCartClick: function(e){

    },
    render: function(){
      this.$el.html(template({
        'name': this.model.get('name'),
        'quantityChanged': this.model.previousAttributes().quantity != this.model.get('quantity'),
        'product_url': this.model.get('product').url,
        'quantity': this.quantity,
        'price':this.model.get('price') * this.quantity,
        'image': this.model.get('product').images instanceof Array && this.model.get('product').images[0] ? this.model.get('product').images[0] : '/images/medium-no_photo.png',
        'preview_image': this.model.get('product').preview_image ? this.model.get('product').preview_image : '/images/medium-no_photo.png'
      }));

      return this;
    },
    close: function(){
      ModalDialogView.prototype.close.apply(this, arguments);

      this.dispose();
    },
    show: function(){
      var self = this;
      this.$overlay.fadeIn();
      this.$el.fadeIn();
    }
  });
});