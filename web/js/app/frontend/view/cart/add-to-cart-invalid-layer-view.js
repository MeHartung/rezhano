/**
 * Created by Денис on 06.06.2017.
 */
define(function(require){
  var ModalDialogView = require('view/dialog/base/modal-dialog-view');

  var template = _.template('\
  <% if (enough) { %>\
    <div class="layer__close"></div>\
    <div class="layer__container">\
      <div class="layer__title">Уточнение количества</div>\
      <div class="add-to-cart-layer__wrap">\
        <p>Товар «<%= name %>» нельзя добавить в корзину в количестве <%= quantity %>&nbsp;<%= quantityUnits %>.</p>\
        <p>Вы можете добавить <%= validQuantity %>&nbsp;<%= validQuantityUnits %> или <%= nextValidQuantity %>&nbsp;<%= nextValidQuantityUnits %></p>\
      </div>\
    </div>\
    <div class="buttons">\
      <a class="button button_black button_yellow-yellow add-valid-quantity" href="<%= urlPrefix %>/cart">Добавить <%= validQuantity %> <%= validQuantityUnits %></a>\
      <a class="button button_black button_yellow-yellow add-next-valid-quantity" href="#">Добавить <%= nextValidQuantity %> <%= nextValidQuantityUnits %></a>\
    </div>\
  <% } else { %>\
    <div class="layer__close"></div>\n\
    <div class="layer__container">\n\
      <div class="layer__title">Уточнение количества</div>\n\
      <div class="add-to-cart-layer__wrap">\n\
        <p>Товар «<%= name %>» нельзя добавить в корзину в количестве <%= quantity %>&nbsp;<%= quantityUnits %>.</p>\
        <p>Минимальное доступное количество для заказа <%= minQuantity %>&nbsp;<%= minQuantityUnits %></p>\n\
      </div>\n\
    </div>\n\
    <div class="buttons">\n\
      <a class="button button_black button_yellow-yellow add-min-quantity" href="<%= urlPrefix %>/cart">Добавить <%= minQuantity %> <%= minQuantityUnits %></a>\n\
    </div>\n\
  <% } %>\
');


  // FIXME Это вынести в отдельный модуль
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
      'click .add-valid-quantity': 'onAddValidQuantity',
      'click .add-next-valid-quantity': 'onAddNextValidQuantity',
      'click .add-min-quantity': 'onAddMinQuantity'
    },
    initialize: function(options){
      this.quantity = +options.quantity;
      this.product = options.product;
      this.cart = options.cart;
      this.cartItem = options.cartItem;
      this.step = +options.product.get('count_step');
      this.minQuantity = +options.product.get('min_count');

      var _q = options.quantity - this.minQuantity,
          modul = _q % this.step,
          min = modul > 0.0001 ? options.quantity - modul : this.minQuantity,
          max = modul > 0.0001 ? min + this.step : options.quantity;

      this.units = 'шт.';

      this.validQuantity = this.formatFloat(min);
      this.nextValidQuantity = this.formatFloat(max);

      ModalDialogView.prototype.initialize.apply(this, arguments);
    },
    onCloseClick: function(e){
      e.preventDefault();

      this.close();
    },
    render: function(){
      var quantityScale = this.getScale(this.quantity);
      var validQuantityScale = this.getScale(this.validQuantity);
      var nextValidQuantityScale = this.getScale(this.nextValidQuantity);
      var minQuantityScale = this.getScale(this.minQuantity);

      this.$el.html(template({
        'validQuantity': this.formatFloat(this.modelToView(+this.validQuantity, validQuantityScale)),
        'nextValidQuantity': this.formatFloat(this.modelToView(+this.nextValidQuantity, nextValidQuantityScale)),
        'step': this.step,
        'quantityUnits': quantityScale ? quantityScale.units : this.units,
        'validQuantityUnits': validQuantityScale ? validQuantityScale.units : this.units,
        'nextValidQuantityUnits': nextValidQuantityScale ? nextValidQuantityScale.units : this.units,
        'minQuantityUnits': minQuantityScale ? minQuantityScale.units : this.units,
        'minQuantity': this.formatFloat(this.modelToView(+this.minQuantity, minQuantityScale)),
        'quantity': this.formatFloat(this.modelToView(+this.quantity, quantityScale)),
        'name': this.product.get('name'),
        'enough': this.quantity >= this.minQuantity
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
    onAddValidQuantity: function (e) {
      e.preventDefault();
      this.addToCart(this.validQuantity);
    },
    onAddNextValidQuantity: function (e) {
      e.preventDefault();
      this.addToCart(this.nextValidQuantity);
    },
    onAddMinQuantity: function (e) {
      e.preventDefault();
      this.addToCart(this.minQuantity);
    },
    addToCart: function (quantity) {
      var self = this;
      this.cartItem
        .set('quantity', quantity)
        .save()
        .done(function(){
          self.cart.items.set(self.cartItem, {
            remove: false
          });
          self.cart.trigger('item:add', self.cartItem, self.cartItem.quantity);
          self.close();
        });
    },
    formatFloat: function ($number) {
      return +$number.toFixed(2);
    },
    /**
     * FIXME всю эту настройку шкал нужно вынести в отдельный модуль
     * Выбирает наиболее подходящую шкалу для конвертации значения
     * @param v
     */
    getScale: function(v){
      var self = this,
          result = null;

      if (!this.product.get('isMeasured')){
        return result;
      }
      if (!result){
        result = quantityScales[0]; //@FIXME выбирать шкалу с мультипликатором 1
      }

      //1. Ищем подходящую шкалу для значения 0.xxx
      var q = +v, scaleAdjusted = false;
      if (q < 1){
        _.each(quantityScales, function(scale){
          //Предполагаем, что шкалы уже отсортированы в порядке возрастания масштаба
          if (scale.multiplicator > result.multiplicator && q*scale.multiplicator > 1){
            result = scale;
            scaleAdjusted = true;

            return false;
          }
        });
      }
      if (!scaleAdjusted){
        _.each(quantityScales, function(scale){
          //Предполагаем, что шкалы уже отсортированы в порядке возрастания масштаба
          if (q >= scale.multiplicator){
            result = scale;
            scaleAdjusted = true;
            return false;
          }
        });
      }

      return result;
    },
    /**
     * Конвертирует отображаемое значение в значение, хранимое в модели (в кг)
     *
     * @param v
     * @param scale
     */
    viewToModel: function(v, scale){
      if (!scale){
        return v;
      }

      return v / scale.multiplicator;
    },
    modelToView: function(v, scale){
      if (!scale){
        return v;
      }

      return v * scale.multiplicator;
    }
  });
});