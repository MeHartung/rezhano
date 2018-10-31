define(function(require){
  var Backbone = require('backbone');

  require('lib/string');

  var template = _.template(require('templates/cart/widget/quantity-widget'));

  /**
   * Представление виджета изменения количества товара в корзине
   */
  return Backbone.View.extend({
    className: 'quantity-widget quantity-wrap',
    defaults:{
      min: 1,
      max: null,
      step: 1
    },
    events:{
      'keydown  .quantity-control__input': 'onCartActionsQuantityInputKeydown',
      'keyup    .quantity-control__input': 'onCartActionsQuantityInputKeyup',
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

      this.$quantityInput =  this.$('.quantity-control__input');
    },
    render: function(){
      this.$el.html(template({
        quantity: this.formatFloat(+this.model.get('quantity')),
        product_stock: this.model.get('product')?this.model.get('product').available_stock:null,
        showButtons: this.options.showButtons
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
      
      if (!isNaN(_val) && isFinite(_val) && this.__valueIsInBounds(_val)) {
        _val = Math.floor(_val / this.step) * this.step;

        if (+self.model.get('quantity') === _val) {
          this.$quantityInput.val(this.formatFloat(+this.model.get('quantity')));
        } else {
          self.model.set({ quantity: _val });
        }

      } else {
        if (value.length > 0){
          this.$quantityInput.val(this.formatFloat(+self.previousValue));
        }
      }
    },
    onQuantityChanged: function(){
      this.$quantityInput.val(this.formatFloat(+this.model.get('quantity')));
    },
    onAppendClick: function(e){
      e.preventDefault();
      var value = this.$quantityInput.val(),
          _val = parseFloat(value);
      if (!isNaN(_val) && isFinite(_val)) {
        this.model.set({ quantity: this.__ensureMaxLimit(_val + this.step) });
      } else {
        this.model.set({ quantity: 1 });
      }
      this.$quantityInput.focus();
    },
    onSubtractClick: function(e){
      e.preventDefault();

      var value = this.$quantityInput.val();
      if (!isNaN(parseFloat(value)) && isFinite(value)) {
        this.model.set({ quantity: Math.max(this.model.get('quantity') - this.step, this.minQuantity)});
      } else {
        this.model.set({ quantity: this.minQuantity });
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
      return $number - Math.floor($number) ? $number.toFixed(3) : $number;
    }
  });
});

