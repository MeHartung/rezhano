/**
 * Created by Денис on 31.05.2017.
 */
define(function (require){
  var FilterWidget = require('view/catalog/filter/widget/filter-widget'),
      slider = require('jquery-ui/widgets/slider');

  var template = _.template('\
<input type="text"<% if (minValueInputName) { %> name="<%= minValueInputName %>"<% } %> placeholder="<%= rangeMin %>" class="slider-min-value" id="<%= minValueInputId %>"<% if (null !== minValue) { %> value="<%= minValue %><% } %>"> \
<input type="text"<% if (maxValueInputName) { %> name="<%= maxValueInputName %>"<% } %> placeholder="<%= rangeMax %>" class="slider-max-value" id="<%= maxValueInputId %>"<% if (null !== maxValue) { %> value="<%= maxValue %><% } %>">\
<div class="slider custom-slider-range">\
  ');

  return FilterWidget.extend({
    events: {
      'change .slider-min-value': 'onMinValueInputChange',
      'change .slider-max-value': 'onMaxValueInputChange',
      'keydown .slider-min-value, .slider-max-value': 'onValueInputKeydown',
      'slide .slider': 'onSlide'
    },
    className: 'filter-widget range-slider range-slider-measurable',
    template: template,
    initialize: function(options){
      this.options = $.extend({
        step: 30
      }, options);

      FilterWidget.prototype.initialize.apply(this, arguments);

      this.valueInputTimer = null;
    },
    render: function(){
      var state = this.model.get('state'),
          $slider = this.$('.slider');

      if ($slider.length){
        $slider.slider('destroy');
      }

      this.$el.html(this.template(this._templateData()));

      this.initializeSlider();

      return this;
    },
    onMinValueInputChange: function(e){
      var minValue = this._correctValue($(e.currentTarget).val());

      this.setValue({
        'min': minValue,
        'max': this.getMaxValue()
      });
    },
    onMaxValueInputChange: function(e){
      var maxValue = this._correctValue($(e.currentTarget).val());

      this.setValue({
        'min': this.getMinValue(),
        'max': maxValue
      });
    },
    onValueInputKeydown: function(e){
      var self = this;

      if (this.valueInputTimer){
        clearTimeout(this.valueInputTimer);
        this.valueInputTimer = null;
      }

      if (e.keyCode == 13){
        e.preventDefault();

        this.updateValueFromInput();

        return false;
      } else {
        this.valueInputTimer = setTimeout($.proxy(this.updateValueFromInput, this), 500)
      }
    },
    getMinValue: function(){
      var value = this.model.get('value');

      return  null === value ? null : value['min'];
    },
    getMaxValue: function(){
      var value = this.model.get('value');

      return  null === value ? null : value['max'];
    },
    setValue: function(value){
      if ($.isArray(value)){
         if (null === value['min'] && null === value['max']){
           value = null;
         }
      }

      if (!this.validate(value)){
        this.isChanging = true;

        this.model.set({
          value: value
        });

        this.isChanging = false;

        return true;
      }

      return false;
    },
    _correctValue: function(value){
      if ('' === value){
        value = null;
      } else {
        value = Number(value);
      }

      return value;
    },
    updateValueFromInput: function(){
      this.valueInputTimer = null;

      var minValue = this._correctValue(this.$('.slider-min-value').val()),
          maxValue = this._correctValue(this.$('.slider-max-value').val());

      if (this.setValue({
          'min': minValue,
          'max': maxValue
      })){
        var sliderValues = this.getSliderValues({
          'min': minValue,
          'max': maxValue
        });

        this.$('.slider').slider('option', 'values', this.getSliderValues([sliderValues['min'], sliderValues['max']]));
        //@FIXME
        this.$('.slider-min-value').trigger('change');
      }
    },
    validate: function(value){
      if ($.isArray(value)){
        var limits = this.model.get('state').limits;

        if (null !== value['min'] && null !== value['max'] && value['max'] < value['min']) {
          return 'Max value is greater than min value';
        }
        // if (value[0] < limits.min){
        //   return 'Min value is less than min limit';
        // }
        // if (value[1] > limits.max){
        //   return 'Max value id greater than max limit';
        // }
      }
    },
    onSlide: function(event, ui){
      var lt = ui.values[0],
          rt = ui.values[1],
          limits = this.model.get('state').limits;

      if (lt <= limits.min){
        lt = '';
      }
      if (rt >= limits.max){
        rt = '';
      }

      this.$('.slider-min-value').val(lt);
      this.$('.slider-max-value').val(rt);

      if (this.valueInputTimer){
        clearTimeout(this.valueInputTimer);
        this.valueInputTimer = null;
      }

      this.valueInputTimer = setTimeout($.proxy(this.updateValueFromInput, this), 500)
    },
    getSliderValues: function(values){
      var limits = this.model.get('state').limits;

      var _vals = values;
      if (null === values['min']){
        values['min'] = limits.min;
      }
      if (null === values['max']){
        values['max'] = limits.max;
      }

      return values;
    },
    reset: function(){
      FilterWidget.prototype.reset.apply(this, arguments);

      //@FIXME: Нужно, чтобы фильтр реагировал на изменение модели
      this.$('input:first').change();
    },
    initializeSlider: function(){
      var state = this.model.get('state'),
          minValue = this.getMinValue(),
          maxValue = this.getMaxValue(),
          values = this.getSliderValues({
            'min': minValue,
            'max': maxValue
          }),
          maxLimit = state.limits.max - (state.limits.max % this.options.step) + this.options.step

      this.$('.slider').slider({
        min: state.limits.min,
        max: maxLimit,
        values: [values['min'], values['max']],
        range: true,
        step: this.options.step,
        disabled: state.limits.min >= state.limits.max,
        slide: $.proxy(this.onSlide, this)
      });
    },
    _templateData: function(){
      var state = this.model.get('state'),
          minValue = this.getMinValue(),
          maxValue = this.getMaxValue(),
          name = this.generateName();

      return {
        id: this.generateId(),
        name: this.generateName(),
        rangeMin: state.limits.min,
        rangeMax: state.limits.max,
        minValue: minValue,
        maxValue: maxValue,
        minValueInputName: name ? name + '[min]' : null,
        maxValueInputName: name ? name + '[max]' : null,
        minValueInputId: this.generateId() + '_min',
        maxValueInputId: this.generateId() + '_max',
        units: this.model.get('units')
      }
    }
  });

  // if(filterState[filter].widget == "asWidgetFormRangeSlider" ||
  //   filterState[filter].widget == "sprWidgetFormRangeSlider" )
  // {
  //   var filterControll = $("#f_" + filter + "");
  //
  //   // нужно добавить нормальную проверку
  //   if(!filterControll.length)
  //     continue;
  //
  //   filterControll.spr_slider('destroy');
  //
  //   filterControll.spr_slider({
  //     min: filterState[filter].limits['min'],
  //     max: filterState[filter].limits['max'],
  //     values: [filterState[filter].value[0], filterState[filter].value[1]],
  //     range: true,
  //     step: 10,
  //     correction: 0
  //   });
  // }
  // if (filterState[filter].widget == "sprWidgetFormRangeSliderEav"){
  //   var filterControll = $("#f_" + filter + "");
  //
  //   // нужно добавить нормальную проверку
  //   if(!filterControll.length)
  //     continue;
  //
  //   for (var value in filterState[filter].choices )
  //   {
  //     var option = filterControll.find("option[value='"+value+"']");
  //
  //     if (filterState[filter].choices[value].enabled == true)
  //       option.removeClass("disabled");
  //     else
  //       option.addClass("disabled");
  //   }
  //   var precision = filterControll.eav_range_slider('option', 'precision');
  //
  //   filterControll.eav_range_slider('destroy');
  //
  //   filterControll.eav_range_slider({ render: false, precision: precision });
  // }
});

