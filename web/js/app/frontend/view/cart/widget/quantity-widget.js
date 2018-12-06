define(function(require){
  var Backbone = require('backbone');

  require('lib/string');

  var template = _.template(require('templates/cart/widget/quantity-widget'));

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

  /**
   * Представление виджета изменения количества товара в корзине
   */
  return Backbone.View.extend({
    className: 'quantity-widget quantity-wrap',
    defaults:{
      min: 1,
      max: null,
      step: 1,
      units: 'шт'
    },
    events:{
      // 'keydown  .quantity-control__input': 'onCartActionsQuantityInputKeydown',
      'change    .quantity-control__input': 'onCartActionsQuantityInputKeyup',
      'click    .quantity-control__up': 'onAppendClick',
      'click    .quantity-control__down': 'onSubtractClick'
    },
    initialize: function(options){
      this.options = $.extend({
        showButtons: true
      }, options);
      
      this.model.on('change:quantity', this.onQuantityChanged, this);
      this.inputTimer = null;
      this.previousValue = this.model.get('quantity').toString();

      if (typeof this.options.min !== 'undefined') this.minQuantity = this.options.min;
      else this.minQuantity = this.defaults.min;

      if (typeof this.options.max !== 'undefined') this.maxQuantity = this.options.max;
      else this.maxQuantity = this.defaults.max;

      if (typeof this.options.step !== 'undefined') this.step = parseFloat(this.options.step);
      else this.step = this.defaults.step;

      // if (typeof this.options.units !== 'undefined') this.units = this.options.units;
      // else this.units = this.defaults.units;
      this.units = this.defaults.units;

      this.$quantityInput =  this.$('.quantity-control__input');

      this.scale = null;

      this.adjustScale();
    },
    render: function(){
      this.$el.html(template({
        quantity: this.formatFloat(this.modelToView(+this.model.get('quantity'))),
        product_stock: this.model.get('product')?this.model.get('product').available_stock:null,
        showButtons: this.options.showButtons,
        units: this.scale ? this.scale.units : this.units
      }));

      this.$quantityInput =  this.$('.quantity-control__input');

      return this;
    },
    onCartActionsQuantityInputKeydown: function(e){
      var value = this.$quantityInput.val();
      if (!isNaN(parseFloat(value)) && isFinite(value) || value.length === 0) {
        this.previousValue = value;
      }

      if (null !== this.inputTimer){
        clearTimeout(this.inputTimer);
        this.inputTimer = null;
      }
    },
    onCartActionsQuantityInputKeyup: function(e){
      var self = this;
      var value = self.$quantityInput.val(),
          _val = parseFloat(value);
      
      if (!isNaN(_val) && isFinite(_val) /*&& this.__valueIsInBounds(_val)*/) {
        // _val = this.formatFloat(Math.floor(_val / this.step) * this.step);

        // if (+self.model.get('quantity') === _val) {
        //   this.$quantityInput.val(this.formatFloat(+this.model.get('quantity')));
        // } else {
          self.model.set({ quantity: self.viewToModel(+_val) });
        // }

      } else {
        if (value.length > 0){
          this.$quantityInput.val(+self.previousValue);
        }
      }
    },
    onQuantityChanged: function(){
      this.adjustScale();

      this.$quantityInput.val(this.formatFloat(this.modelToView(+this.model.get('quantity'))));

      var units = this.scale ? this.scale.units : this.units;
      var $units = this.$('.quantity-widget__units');

      if ($units.text() !== units) {
        $units.text(units);
        $units.css({'color': '#ffcf40'}).animate({'color': '#000'});
      }
    },
    onAppendClick: function(e){
      e.preventDefault();
      var value = this.$quantityInput.val(),
          _val = parseFloat(value);
      if (!isNaN(_val) && isFinite(_val)) {
        this.model.set({ quantity: this.viewToModel(+this.formatFloat(this.__ensureMaxLimit(_val + this.scaledStep()))) });
      } else {
        this.model.set({ quantity: this.viewToModel(this.scaledMinQuantity()) });
      }
      this.$quantityInput.focus();
    },
    onSubtractClick: function(e){
      e.preventDefault();

      var value = +this.$quantityInput.val();
      if (!isNaN(parseFloat(value)) && isFinite(value)) {
        this.model.set({ quantity: this.viewToModel(+this.formatFloat(Math.max(value - this.scaledStep(), this.scaledMinQuantity())))});
      } else {
        this.model.set({ quantity: this.viewToModel(this.scaledMinQuantity()) });
      }
      this.$quantityInput.focus();
    },
    /**
     * Возвращает наибольшее допустимое значение количества по отношению к заданному
     *
     * Если заданное количество больше максимального, возвращает максимальное количество. В противном случае
     * возвращает заданное количество.
     *
     * @param v Number
     * @returns Number
     * @private
     */
    __ensureMaxLimit: function(v){
      if (null === this.maxQuantity){
        return v;
      }

      return Math.min(parseInt(this.maxQuantity / this.step) * this.step, v);
    },
    __valueIsInBounds: function(v){
      return v >= this.minQuantity && (Math.abs(v - this.__ensureMaxLimit(v)) < 0.001);
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
      var q = +this.model.get('quantity'), scaleAdjusted = false;
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
    },
    scaledStep: function(){
      return this.scale ? this.scale.multiplicator*this.step : this.step;
    },
    scaledMinQuantity: function(){
      return this.scale ? this.scale.multiplicator*this.minQuantity : this.minQuantity;
    }
  });
});

