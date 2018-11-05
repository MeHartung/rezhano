define(function (require) {
    var FilterWidget = require('view/catalog/filter/widget/filter-widget');

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
        // className: 'product-filter__item',
        events: {
            'change input': 'onCheckboxChange'
        },
        initialize: function(options){
          var opts = _.extend({
              clearable: true
          }, options);

          this.clearable = opts.clearable;

          FilterWidget.prototype.initialize.apply(this, arguments);
        },
        render: function () {
            var choices = this.model.get('state').choices,
                value = this.model.get('value');

            $.each(choices, function (idx, choice) {
                choice.checked = $.inArray(choice.id, value) >= 0 || $.inArray(String(choice.id), value) >= 0;
            });

            this.$el.html(template({
                id: this.generateId(),
                name: this.generateName(),
                label: this.schema.label,
                choices: choices,
                type: this.schema.type || 'checkbox'
            }));

        },
        onCheckboxChange: function (e) {
            var _value = [];

            this.$(':checked').each(function () {
                _value.push($(this).val());
            });

            if (!_value.length) {
                _value = null;
            }

            this.model.set('value', _value);
        },
        reset: function(){
            FilterWidget.prototype.reset.apply(this, arguments);

            //@FIXME: Нужно, чтобы фильтр реагировал на изменение модели
            this.$('input:first').change();
        }
    })
});