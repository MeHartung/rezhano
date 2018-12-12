define(function(require){
  var Backbone = require('backbone'),
    CommonView = require('view/common/common-view'),
    ContactsForm = require ('view/contacts/contacts-form'),
    Question = require('model/text/question'),
    MapViewDialog = require('view/common/map-view-dialog');

  return CommonView.extend({
    events: {
      'click .maps-link' : 'onAddressClick'
    },
    initialize: function(options){
      CommonView.prototype.initialize.apply(this, [options]);
      this.mapViewDialog = null;

      // var q = new Question();
      // q.set('text', 'asd');
      // q.save();
      this.questionForm = new ContactsForm({
        model: Question
      });

    },
    render: function(){
      this.questionForm.setElement(this.$('#questionForm'));
      this.questionForm.render();

      return this;
    },
    onAddressClick: function (e) {
      e.preventDefault();

      this.mapViewDialog = new MapViewDialog({
        model: new Backbone.Model({
          address: $(e.currentTarget).attr('data-address')
        }),
      });
      this.mapViewDialog.render().$el.appendTo($('body'));

      this.mapViewDialog.open();
    }
  });
});