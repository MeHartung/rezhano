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
      this.stores = new Backbone.Collection(ObjectCache.Stores || {});

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
      var self = this;
      var currentStore = self.stores.where({fullAddress:$(e.currentTarget).attr('data-address')})[0];
      this.mapViewDialog = new MapViewDialog({
        model: new Backbone.Model({
          address: $(e.currentTarget).attr('data-address'),
          store: currentStore.attributes
        }),
      });
      // console.log(currentStore.attributes);
      this.mapViewDialog.render().$el.appendTo($('body'));

      this.mapViewDialog.open();
    }
  });
});