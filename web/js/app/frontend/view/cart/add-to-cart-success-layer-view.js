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
            <a href="<%= product_url %>" class="add-to-cart-layer__image ">\
              <img src="<%= preview_image %>" alt="">\
            </a>\
            <div class="add-to-cart-layer__info">\
              <a href="<%= product_url %>" class="add-to-cart-layer__product-name"><%= name %></a>\
              <div class="add-to-cart-layer__product-value">количество <%= quantity %> <%= units %> &nbsp;&nbsp; <%= price.toCurrencyString() %></div>\
            </div>\
          </div>\
        </div>\
        <div class="buttons">\
          <a class="button button_black" href="<%= urlPrefix %>/cart">Перейти в корзину</a>\
          <a class="button button_black continue-shopping" href="#">Продолжить покупки</a>\
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

  var quantityScales = [
    {
      units: "кг",
      multiplicator: 1
    },
    {
      units: "г",
      multiplicator: 1000
    }
  ];

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

      this.units = 'шт';
      this.scale = null;

      this.adjustScale();

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
      var units = 'шт';
      if (this.model.get('product').isMeasured) {
        units = this.model.get('product').units ? this.model.get('product').units : '';
      }

      this.$el.html(template({
        'name': this.model.get('name'),
        'quantityChanged': this.model.previousAttributes().quantity != this.model.get('quantity'),
        'product_url': this.model.get('product').url,
        'quantity': this.formatFloat(this.modelToView(+this.quantity)),
        'price':this.model.get('price') * this.quantity,
        'image': this.model.get('product').images instanceof Array && this.model.get('product').images[0] ? this.model.get('product').images[0] : '/images/medium-no_photo.png',
        'preview_image': this.model.get('product').preview_image ? this.model.get('product').preview_image : '/images/medium-no_photo.png',
        'mountBg': this.model.get('product').background ? 'add-to-cart-layer__image_yellow' : '',
        'units': this.scale ? this.scale.units : this.units
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
    },
    formatFloat: function ($number) {
      return +$number.toFixed(2);
    },
    /**
     * Выбирает наиболее подходящую шкалу для конвертации значения
     * @param v
     */
    adjustScale: function(v){
      var self = this;

      if (!this.model.get('product').isMeasured){
        this.scale = null;
        return;
      }
      if (!this.scale){
        this.scale = quantityScales[0]; //@FIXME выбирать шкалу с мультипликатором 1
      }

      //1. Ищем подходящую шкалу для значения 0.xxx
      var q = +this.quantity, scaleAdjusted = false;
      if (q < 1){
        _.each(quantityScales, function(scale){
          //Предполагаем, что шкалы уже отсортированы в порядке возрастания масштаба
          if (scale.multiplicator > self.scale.multiplicator && q*scale.multiplicator > 1){
            self.scale = scale;
            scaleAdjusted = true;
            return false;
          }
        });
      }
      if (!scaleAdjusted){
        _.each(quantityScales, function(scale){
          //Предполагаем, что шкалы уже отсортированы в порядке возрастания масштаба
          if (q >= scale.multiplicator){
            self.scale = scale;
            scaleAdjusted = true;
            return false;
          }
        });
      }

    },
    /**
     * Конвертирует отображаемое значение в значение, хранимое в модели (в кг)
     *
     * @param v
     */
    viewToModel: function(v){
      if (!this.scale){
        return v;
      }

      return v / this.scale.multiplicator;
    },
    modelToView: function(v){
      if (!this.scale){
        return v;
      }

      return v * this.scale.multiplicator;
    }
  });
});