define(function(require){
  var ListView = require('view/base/list-view'),
    UserDocumentLIstView = require('view/user/registration/registration-doc-load-item');

  return ListView.extend({
    template: '',
    itemView: UserDocumentLIstView,
    initialize: function(options){
      this.options = options;
      ListView.prototype.initialize.apply(this, arguments);
    },
    _createItemView: function(item, index){
      return new this.itemView({ model: item, index: index});
    },
    render: function(){
      ListView.prototype.render.apply(this, arguments);

      return this;
    }
  });
});