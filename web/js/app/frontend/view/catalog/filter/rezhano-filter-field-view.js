define(function (require) {
    var FilterField = require('view/catalog/filter/catalog-filter-field-view');

    return FilterField.extend({
        events: _.extend({}, FilterField.prototype.events, {
            'click .product-filter__link': 'onProductFilterLinkClick'
        }),
        onProductFilterLinkClick: function (e) {
            e.preventDefault();

            this.toggle();
        },
        toggle: function () {
            // this.$el.toggleClass('deployed');
        },
        updateState: function () {
            var val = this.model.get('value');
            if ($.isArray(val) && val.length) {

              this.$el.addClass('active');
            } else {
                this.$el.removeClass('active');
            }
        }
    })
});