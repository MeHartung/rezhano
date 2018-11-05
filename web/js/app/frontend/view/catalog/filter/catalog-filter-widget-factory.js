/**
 * Created by Денис on 31.05.2017.
 */
define(function(require){
  var //IndependentFlagsWidget = require('view/catalog/filter/widget/filter-widget-independent-flags'),
      RangeSliderWidget = require('view/catalog/filter/widget/filter-widget-range-slider'),
      // EavRangeSliderWidget = require('view/catalog/filter/widget/filter-widget-eav-range-slider'),
      SelectCheckboxWidget = require('view/catalog/filter/widget/filter-widget-choice-rezhano');
      // ExpandableSelectWidget = require('view/catalog/filter/widget/filter-widget-expandable-select');

  var widgetMap = {
//    'asWidgetFormIndependentFlags': IndependentFlagsWidget,
    'range_slider': RangeSliderWidget,
    // 'sprWidgetFormRangeSliderEav': EavRangeSliderWidget,
    'choice_expanded': SelectCheckboxWidget
    // 'asWidgetFormFilterSelectCheckbox': SelectCheckboxWidget,
    // 'sprWidgetFormFilterSelectCheckboxExpandable': ExpandableSelectWidget
  };

  var WidgetFactory = function(schema){
    this.schema = schema;
  }

  WidgetFactory.prototype.create = function(model, field) {
    var fieldSchema = this.schema[field],
        widget;

    if (this.canCreate(field)){
      widget = new widgetMap[fieldSchema['widget']]({
        id: field,
        schema: fieldSchema,
        model: model
      });
    }

    return widget;
  }

  WidgetFactory.prototype.canCreate = function(field){
    if ('undefined' === typeof this.schema[field] ||
        'undefined' === typeof this.schema[field].widget){
      return false;
    }

    return 'undefined' !== typeof widgetMap[this.schema[field].widget];
  }

  return WidgetFactory;
});
