define(function(require){
  var Backbone = require('backbone');

  require('jquery.daterange');

  return Backbone.View.extend({
    initialize: function (options) {
      this.orderCollection = options.orders;
    },
    render: function () {
      this.$('#datePicker input').daterange({
        dateFormat: 'dd.mm.yy',
        changeMonth: false,
        changeYear: false,
        onClose: $.proxy(this.onFormSubmit, this)
      });

      this.$('.custom-border-select select').selectmenu({
        change: $.proxy(this.onFormSubmit, this)
      });
      return this;
    },
    onFormSubmit: function () {
      var data = this._getFormData();
      this.orderCollection.fetch({
        dataType: 'json',
        method: 'POST',
        data: JSON.stringify(data),
        url: urlPrefix + '/cabinet/profile'
      });
    },
    _getFormData: function () {
      // var data = this.$el.serializeArray();
      var data = {
        'date': this.$('#order_filter_date').val(),
        'city': this.$('#order_filter_city').val(),
      };

      return data;
    }
  })
});