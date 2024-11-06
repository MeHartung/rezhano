define(function(require) {
  var Backbone = require('backbone');
  var ListItemView = require('view/base/list-item-view');

  var template = _.template('' +
    '<div class="payment-info__product">\n' +
    ' <span class="payment-info__product-name"><%= name %></span>\n' +
    ' <span class="payment-info__product-value"><%= quantity %> <%= units %> × <%= price.toFixed() %> ₽</span>\n\n   ' +
    '</div>\n');

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

  return ListItemView.extend({
    className: 'product-item',
    defaults: {
      units: 'шт.'
    },
    initialize: function(options){
      ListItemView.prototype.initialize.apply(this, arguments);

      this.scale = null;
      this.units = this.defaults.units;
      this.adjustScale();
    },
    render: function () {
      this.$el.html(template({
        name: this.model.get('name'),
        quantity: this.formatFloat(this.modelToView(+this.model.get('quantity'))),
        units: this.scale ? this.scale.units : this.units,
        price: this.model.get('product').measuredPartPrice
      }));
      return this;
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
    modelToView: function(v){
      if (!this.scale){
        return v;
      }

      return v * this.scale.multiplicator;
    },
  })
});