/**
 * Created by Денис on 31.05.2017.
 */
define(function(require){
  var Backbone = require('backbone');

  var template = _.template('\
  <% if (editPanelHtml) { %>\
    <div class="filter-edit-controls img-block">\
      <%= editPanelHtml %>\
    </div>\
  <% } %>\
  <% if (showTitle) { %>\
    <a href="#" class="filter-section__title product-filter__link"><%= label %> <i class="ui-icon"></i></a>\
  <% } %>\n\
  <div class="product-filter__filter filter-section__content">\
    <span class="product-filter__row">\
      <a href="#" class="product-filter__clear widget-reset-link">\
        <span>очистить</span>\
      </a>\
    </span>\
  </div>\
');

  return Backbone.View.extend({
    className: "product-filter__item",
    events: {
      'click .filter-section__title': 'onFilterCollapseLinkClick',
      'click .widget-reset-link': 'onFilterResetLinkClick'
    },
    initialize: function(options){
      this.options = options;

      this.editPanelHtml = null;

      var $editPanel = this.$('.filter-edit-controls');
      if ($editPanel.length){
        this.editPanelHtml = $editPanel.html();
      }

      var fieldSchema = this.getSchema(),
          fieldState = this.getState();

      this.model = new Backbone.Model({
        id: options.id,
        name: options.name,
        showCollapsed: fieldSchema.showCollapsed,
        label: fieldSchema.label,
        showTitle: true, //@FIXME Здесь должна быть такая логика: <?php if (!$filterField instanceof asPropelBooleanFilterField && $filterField->getShowTitle() === true): ?>
        value: fieldState.value,
        state: fieldState.state,
        units: 'undefined' !== typeof fieldSchema.units ? fieldSchema.units : null
      });

      this.listenTo(this.model, 'change:value', this.onValueChange);
      this.listenTo(options.filter, 'change:state', this.onFilterStateChange);

      this.descriptionIcon = null;

      this.widget = options.widgetFactory.create(this.model, options.id);

      options.filter.values.add(this.model);
    },
    render: function(){

      this.widget.undelegateEvents();
      this.widget.$el.remove();

      if (this.descriptionIcon) {
        this.descriptionIcon.remove();
      }

      this.$el.html(template({
        id: this.model.get('id'),
        label: this.model.get('label'),
        showTitle: this.model.get('showTitle'),
        editPanelHtml: this.editPanelHtml
      }));

      if (this.model.get('showCollapsed')){
        this.$el.addClass('filter-section_collapse');
      }

      //@FIXME: Эта строчка добавлена здесь, чтобы очищать предварительно отрендеренные с сервера контролы. Не уверен, что это нужно делать именно так
      this.$('.filter-section__content').html('');
      this.$('.filter-section__content').prepend(this.widget.$el);
      this.widget.delegateEvents();

      this.widget.render();

      if (this.descriptionIcon){
        this.descriptionIcon.$el.appendTo(this.$('.filter-section-title'));
        this.descriptionIcon.delegateEvents();
        this.descriptionIcon.render();
      }

      this.updateState();

      return this;
    },
    onFilterCollapseLinkClick: function(e){
      e.preventDefault();

      this.toggleCollapsed();
    },
    toggleCollapsed: function(){
      this.$('.filter-section__content').stop().slideToggle();
      this.$el.toggleClass('filter-section_collapse');
    },
    onValueChange: function(){
      this.updateState();
    },
    updateState: function(){
      if (null !== this.model.get('value')){
        this.$el.addClass('changed');
      } else {
        this.$el.removeClass('changed')
      }
    },
    onFilterResetLinkClick: function(e){
      e.preventDefault();

      this.widget.reset();
    },
    onFilterStateChange: function(filter){
      var filterState = filter.get('state');

      if ('undefined' !== typeof filterState[this.options.id]){
        this.model.set({
          state: filterState[this.options.id].state
        });
      }
    },
    getSchema: function(){
      var filterSchema = this.options.filter.get('schema');

      return filterSchema[this.options.id];
    },
    getState: function(){
      var filterState = this.options.filter.get('state');

      return filterState[this.options.id];
    }
  });
});