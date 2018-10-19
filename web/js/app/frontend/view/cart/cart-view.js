define(function(require) {
    var Backbone = require('backbone'),
        CartItemListView = require('view/cart/list-view/cart-item-list-view'),
        CommonView = require('view/common/common-view'),
        TotalsPanelView = require('view/cart/cart-totals-panel')
    ;

    return CommonView.extend({
        initialize: function (options) {
            this.order = options.cart;
            options.cartWidget = false;
            options.search = false;

            CommonView.prototype.initialize.apply(this, arguments);
            this.cartItemListView = new CartItemListView({
                collection: this.order.items
            });

            this.totalsPanelView = new TotalsPanelView({
                model: this.order
            });
        },
        render: function () {
            CommonView.prototype.render.apply(this, arguments);

            this.cartItemListView.setElement(this.$('.cards-container'));
            this.cartItemListView.render();

            this.totalsPanelView.setElement(this.$('.cards-container__payment-info'));
            this.totalsPanelView.render();

            return this;
        }
    })
});