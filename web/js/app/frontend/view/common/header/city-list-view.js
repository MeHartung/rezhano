/**
 * Created by Dancy on 15.09.2017.
 */
define(function(require){
  var ListView = require('view/base/list-view'),
      CityListItem = require('view/common/header/city-list-item-view');

  return ListView.extend({
    tagName: 'ul',
    className: 'list-stores',
    itemView: CityListItem,
    initialize: function(options){
      this.location = options.location;

      ListView.prototype.initialize.apply(this, arguments);
    },
    _createItemView: function(item, index){
      return new this.itemView({
        model: item,
        index: index,
        location: this.location
      })
    }
  });
});