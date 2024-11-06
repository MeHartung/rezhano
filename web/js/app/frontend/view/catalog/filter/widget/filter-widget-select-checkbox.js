/**
 * Created by Денис on 05.06.2017.
 */
define(function(require){
  var FilterWidget = require('view/catalog/filter/widget/filter-widget');

  require('jquery-checkbox');

  var template = _.template('\
      <% _.each(choices, function(choice){ %>\
    <span class="product-filter__custom-checkbox">\
      <label>\
        <input name="<%= name %>[]" type="<%= type %>" class="checkbox" value="<%= choice.id %>" id="<%= id %>_<%= choice.id %>"<% if (choice.checked) { %> checked<% } %> class="checkbox filter-widget<% if (!choice.enabled){ %> disabled<% } %>" style="display: none;">\
        <span class="checkbox-link">\
            <%= choice.value.trim() %>\
            <span class="custom-checkbox"></span>\n\
        </span>\
      </label>\
    </span>\
  <% }); %>\
</div>\
        ');

  return FilterWidget.extend({
    events: {
      'change input': 'onCheckboxChange'
    },
    initialize: function(){
      FilterWidget.prototype.initialize.apply(this, arguments);

      this.fadeTimer = null;
    },
    render: function(){
      var choices = this.model.get('state').choices,
          value = this.model.get('value');

      $.each(choices, function(idx, choice){
        choice.checked = $.inArray(choice.id, value) >= 0 || $.inArray(String(choice.id), value) >= 0;
      })

//      this.$('input[type="checkbox"]').checkbox('destroy');

      this.$('*').off('.extended_scb');

      this.$el.html(template({
        id: this.generateId(),
        name: this.generateName(),
        label: this.schema.label,
        choices: choices,
        type: this.schema.type || 'checkbox'
      }));

      // this.$('input[type="checkbox"]').checkbox();
    },
    onCheckboxChange: function(e){
      var _value = [];

      this.$(':checked').each(function(){
        _value.push($(this).val());
      })

      if (!_value.length){
        _value = null;
      }

      this.model.set('value', _value);
    },
    reset: function(){
      FilterWidget.prototype.reset.apply(this, arguments);

      //@FIXME: Нужно, чтобы фильтр реагировал на изменение модели
      this.$('input:first').change();
    },
    onLabelMouseEnter: function(e){
      this.changeCheckBoxFaded(e.currentTarget, true);
      if (this.fadeTimer) {
        clearTimeout(this.fadeTimer);
        this.fadeTimer = null;
      }
    },
    onLabelMouseLeave: function(e){
      var self = this;
      this.fadeTimer = setTimeout(function(){
        self.changeCheckBoxFaded(e.currentTarget, false);
      }, 50);
    },
    changeCheckBoxFaded: function(context, fadded) {
      var $cb = $(context).siblings('input[type="checkbox"]'),
          $checkboxes = this.$('input[type="checkbox"]');

      if (fadded) {
        $checkboxes.removeClass('filter-widget')
                   .addClass('filter-checkbox-faded');

        $cb.removeClass('filter-checkbox-faded')
           .addClass('filter-widget');

      } else {
        $checkboxes.removeClass('filter-checkbox-faded')
                   .addClass('filter-widget');
      }
    },
    onLabelClick: function(e){
      e.preventDefault();

      var $cb = $(e.currentTarget).siblings('input[type="checkbox"]');

      this.model.set('value', [$cb.val()]);

      this.$('input[type="checkbox"]:first').change();
    }
  });
})