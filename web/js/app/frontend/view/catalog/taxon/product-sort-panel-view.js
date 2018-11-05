define(function (require) {
    var Backbone = require('backbone'),
        WidgetFactory = require('view/catalog/filter/catalog-filter-widget-factory'),
        FilterField = require('view/catalog/filter/rezhano-filter-field-view')//,
    ;



//   var template = _.template('\
//     <span class="product-list-sort__title">Сортировать:</span>\
//       <% _.each(sortColumns, function(text, name) { %>\n\
//         <a class="product-list-sort__type <% if (name === sorting.column) { %>product-list-sort__type_active product-list-sort__type_price <% if (sorting.order == \'desc\') { %>product-list-sort__type_price_down<% } else { %>product-list-sort__type_price_up<% } %>" title="<% if (sorting.order == \'desc\') { %>По убыванию<% } else { %>По возрастанию<% } %><% } %>"\
//               data-order-column="<%= name %>" \
//               data-order-direction-next="<%= sorting.next %>">\
//           <%= text %> \
//         </a>\
//       <% }); %>\
//       <span class="product-list-sort__my-region custom-checkbox">\n' +
// '                    <label>\n' +
// '                      <input type="checkbox" class="checkbox"<% if (sorting.icrf) { %> checked=""<% } %>>\n' +
// '                      <span class="product-list-sort__checkbox custom-checkbox__checkbox"></span>\n' +
// '                      <span>Сначала предложения в моём регионе</span>\n' +
// '                    </label>\n' +
// '                  </span>\
//     ');


  return FilterField.extend({
    className: 'product-filter__item product-list-sort',
    // events: {
    //   'click .product-list-sort__type': 'sorting'
    // },
    initialize: function (options) {
      this._schema = {
        label: 'сортировать',
        type: 'radio',
        widget: 'choice_expanded',
        showCollapsed: false
      };
      this.options = options = _.extend({
          id: 'sort',
          name: 'sort',
          widgetFactory: new WidgetFactory({
              sort: this._schema
          })
      }, options);

      this.options.filter.on('filtered', this.render, this);

      // this.model = new Backbone.Model({
      //   value: [sort.column],
      //   name: 'sort'
      // });

      FilterField.prototype.initialize.call(this, options);
    },
    // render: function () {
    //   var sort = this.options.filter.get('sort'),
    //     order = sort ? sort.order : 'none';
    //
    //   this.$el.html(template({
    //     sorting: {
    //       order: order,
    //       column: $.inArray(order, ['desc', 'asc']) !== -1 ? sort.column : 'none',
    //       next: sort ? sort.next : 'asc',
    //       icrf: sort.icrf
    //     },
    //     sortColumns: this.options.filter.get('sortColumns') || []
    //   }));
    //
    // },
    onValueChange: function(){
      this.sorting();
      this.updateState();
    },
    sorting: function () {
      var filter = this.options.filter;

      var sort = filter.get('sort'),
          val = this.model.get('value');

      val = 'object' === typeof val && val.length ? val[0] : null;

        var newSort = {
          column: val,
          order: 'rank' === val ? 'asc' : 'desc',//element.data("order-direction-next"),
          next: 'rank' === val ? 'asc' : 'desc' //sort.next
        };

        filter.set('sort', newSort);

        filter.getContent();

        this.render();

//        this.$el.removeClass('deployed');
      },
      getSchema: function(){
        return this._schema;
      },
      getState: function(){
         var sort = this.options.filter.get('sort'),
             order = sort ? sort.order : 'none',
             sortColumns = this.options.filter.get('sortColumns'),
             sortChoices = [];

          _.each(sortColumns, function(name, id){
              sortChoices.push({ id: id, value: name, enabled: true });
          });

        return _.extend({
            state: {
                choices: sortChoices
            },
            value: [sort.column]
          }
        );
      }

  });


});