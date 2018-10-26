/**
 * Created by Денис on 06.06.2017.
 */
define(function (require) {
  var ModalDialogView = require('view/dialog/base/modal-dialog-view');

  var template = _.template('\
        <div class="layer__close"></div>\
        <div class="layer__container">\
          <div class="layer__images">\
            <a href="<%= product_url %>" class="show-full-image">\
              <img src="<%= preview_image %>" alt="">\
            </a>\
            <a href="<%= product_url %>" class="show-full-image">\n              <img src="<%= preview_image %>" alt="">\n            </a>\
          </div>\
          <div class="layer__text">\
            <h2 class="layer__title"><%= name %></h2>\
            <div class="add-to-cart-layer__wrap">\
              <div class="add-to-cart-layer__info">\
                <div class="dd-to-cart-layer__info-type">\
                  <span class="info-type-sign__title">Тип:</span>\
                  <span class="info-type-sign__text"></span>\
                </div>\
                 <div class="dd-to-cart-layer__info-type">\
                    <span class="info-type-sign__title">Выдержка:</span>\
                    <span class="info-type-sign__text"></span>\
                 </div>\
                 <div class="dd-to-cart-layer__info-type">\
                   <span class="info-type-sign__title">Вкус:</span>\
                   <span class="info-type-sign__text"></span>\
                 </div>\
                  <div class="dd-to-cart-layer__info-type">\
                    <span class="info-type-sign__title">С чем едят:</span>\
                    <span class="info-type-sign__text"></span>\
                  </div>\
                <div class="add-to-cart-layer__product-value"><%= quantity %> шт. &nbsp;&nbsp; <%= price.toCurrencyString() %></div>\
                    <div class="product-item__characteristics">\
                      <span class="product-item__quantity">300 г  / </span>\
                      <span class="product-item__price">1&nbsp;200&nbsp;₽</span>\
                   </div>\
                    <a href="" class="button">\
                      <span>В корзину</span>\
                    </a>\
                   <div class="product-item__controls">\
                      <span class="controls-title">количество</span>\
                      <span class="controls-item controls-item__increase"></span>\
                      <input type="text" class="custom-input" value="0">\
                      <span class="controls-item controls-item__reduce"></span>\
                   </div>\
                    </div>\
                    </div>\
                  </div>\
                   <div class="buttons">\
                     <a class="button">Перейти в корзину</a>\
                     <a class="button white continue-shopping" href="#">Продолжить покупки</a>\n        </div>\
                  </div>\
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
    initialize: function (options) {
      this.quantity = options.quantity;

      ModalDialogView.prototype.initialize.apply(this, arguments);
    },
    onCloseClick: function (e) {
      e.preventDefault();

      this.close();
    },
    onContinueClick: function (e) {
      e.preventDefault();

      this.close();
    },
    onShowCartClick: function (e) {

    },
    render: function () {
      var _self = this;

      this.$el.html(template({
        'name': this.model.get('name'),
        'quantityChanged': this.model.previousAttributes().quantity != this.model.get('quantity'),
        'product_url': this.model.get('product').url,
        'quantity': this.quantity,
        'price': this.model.get('price') * this.quantity,
        'image': this.model.get('product').images instanceof Array && this.model.get('product').images[0] ? this.model.get('product').images[0] : '/images/medium-no_photo.png',
        'preview_image': this.model.get('product').preview_image ? this.model.get('product').preview_image : '/images/medium-no_photo.png'
      }));

      $(function () {
        _self.$('.layer__images').slick({
          dots: false,
          arrows: true,
          infinite: true
        });
      });


      return this;
    },
    close: function () {
      ModalDialogView.prototype.close.apply(this, arguments);

      this.dispose();
    },
    show: function () {
      var self = this;
      this.$overlay.fadeIn();
      this.$el.fadeIn();
    }
  });
});