/**
 * Created by Денис on 31.05.2017.
 */
define(function(require){
  var FilterFieldView = require('view/catalog/filter/catalog-filter-field-view');

  /**
   * Заглушка для поля фильтра для полей, у которых еще нет своего виджета
   * Она только слушает события с полем типа сворачивания/разворачивания, сброса и т.п.
   */
  return FilterFieldView.extend({
    initialize: function(){
      FilterFieldView.prototype.initialize.apply(this, arguments);

      if (this.model.get('showCollapsed') && null !== this.model.get('value')){
        this.toggleCollapsed();
      }
    },
    render: function(){
      //Ничего не перерисовываем, так как не умеем - виджета нет
      return this;
    }
  })
})