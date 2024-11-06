/**
 * Created by Dancy on 20.09.2017.
 */
define(function(require){
  var ModalDialog = require('view/dialog/base/modal-dialog-view'),
      Article = require('model/text/article');

  var dialogTemplate = _.template('\
<div class="layer__close"></div>\
<div class="layer__in">\
  <h2><%= title %></h2>\
  <div class="agreement-text">\
    <%= text %>\
  </div>\
</div>\
');

  return ModalDialog.extend({
    events: _.extend({}, ModalDialog.events, {
      'click .layer__close': 'onCloseButtonClick'
    }),
    template: dialogTemplate,
    initialize: function(options){
      var self = this;

      this.model = new Article({
        slug: 'oferta'
      });

      this.model.fetch();

      this.listenTo(this.model, 'change', this.render, this);

      ModalDialog.prototype.initialize.apply(this, arguments);
    },
    render: function(){
      this.$el.html(this.template({
        title: this.model.get('title') || "Условия обслуживания",
        text: this.model.get('text')
      }));

      this._restorePosition();

      return this;
    }
  });
});