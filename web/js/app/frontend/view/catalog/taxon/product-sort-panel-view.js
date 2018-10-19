define(function (require) {
  var Backbone = require('backbone');


  var template = _.template('\
    <span class="product-list-sort__title">Сортировать:</span>\
      <% _.each(sortColumns, function(text, name) { %>\n\
        <a class="product-list-sort__type <% if (name === sorting.column) { %>product-list-sort__type_active product-list-sort__type_price <% if (sorting.order == \'desc\') { %>product-list-sort__type_price_down<% } else { %>product-list-sort__type_price_up<% } %>" title="<% if (sorting.order == \'desc\') { %>По убыванию<% } else { %>По возрастанию<% } %><% } %>"\
              data-order-column="<%= name %>" \
              data-order-direction-next="<%= sorting.next %>">\
          <%= text %> \
        </a>\
      <% }); %>\
      <span class="product-list-sort__my-region custom-checkbox">\n' +
'                    <label>\n' +
'                      <input type="checkbox" class="checkbox"<% if (sorting.icrf) { %> checked=""<% } %>>\n' +
'                      <span class="product-list-sort__checkbox custom-checkbox__checkbox"></span>\n' +
'                      <span>Сначала предложения в моём регионе</span>\n' +
'                    </label>\n' +
'                  </span>\
    ');


  return Backbone.View.extend({
    className: 'product-list-sort',
    events: {
      'click .product-list-sort__type': 'sorting',
      'change .product-list-sort__my-region input': 'onIcrfCheckboxChange'
    },
    initialize: function (options) {
      this.options = options;

      this.render();

      this.options.filter.on('filtered', this.render, this);
    },
    render: function () {
      var sort = this.options.filter.get('sort'),
        order = sort ? sort.order : 'none';

      this.$el.html(template({
        sorting: {
          order: order,
          column: $.inArray(order, ['desc', 'asc']) !== -1 ? sort.column : 'none',
          next: sort ? sort.next : 'asc',
          icrf: sort.icrf
        },
        sortColumns: this.options.filter.get('sortColumns') || []
      }));

    },
    onIcrfCheckboxChange: function(e){
        var filter = this.options.filter;

        var sort = filter.get('sort'),
            element = $(e.currentTarget);

        if (element) {
            var newSort = _.extend({}, sort, {
                icrf: element.is(':checked')
            });

            filter.set('sort', newSort);

            filter.getContent();
        }
    },
    sorting: function (event) {
      event.preventDefault();

      var filter = this.options.filter;

      var sort = filter.get('sort'),
          element = $(event.currentTarget);

      if (element) {
        var newSort = {
          column: element.data("order-column"),
          order: element.data("order-direction-next"),
          next: sort.next
        }

        filter.set('sort', newSort);

        filter.getContent();

        this.render();
      }
    }
  });

  return SortingView;


});