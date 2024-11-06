define(function(require){
  var $ = require('jquery'),
      CommonView = require('view/common/common-view'),
      datepicker = require('jquery-ui/widgets/datepicker');

  require('jquery-ui/i18n/datepicker-ru');

  return CommonView.extend({
    events: {
      'click .formSubmit': 'onSubmitForm'
    },
    initialize: function(options) {
      this.order = options.cart;

      options.cartWidget = false;
      options.search = false;

      CommonView.prototype.initialize.apply(this, arguments);
    },
    render: function(){
      CommonView.prototype.render.apply(this, arguments);
      this.$( "#deliveryTabs" ).tabs({
        // activate: $.proxy(this.onActivateTab, this)
      });
      this.$('#typePickup_shippingDate,#typeCourier_shippingDate').datepicker(_.extend({
          showOn: "button",
          buttonImage: '/images/datepicker-icon.png',
          buttonImageOnly: true,
          dateFormat: 'dd.mm.yy',
          minDate: new Date(this.order.get('closest_pickup_date'))
      }, datepicker.regional['ru']));

      return this;
    },
    // onActivateTab: function (event, ui) {
    //   var selectedTab = ui.newPanel.attr('id');
    // },
    onSubmitForm: function () {
      this.$( "#deliveryTabs").tabs('instance').panels.find('form:visible').submit();
    }
  });
});