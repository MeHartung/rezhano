/**
 * Backbone collection view
 *
 * options:
 *  * container: Collection element list container selector;
 *  * template: Template to render for current view;
 *  * itemView: A view for each item to render;
 *  * emptyView: A view for empty collection;
 *  * autoHide: Hide container if the collection is empty.
 *
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 */
define(function(require){
  var Backbone = require('backbone');

 /**
  *
  * @type {*}
  */
  return Backbone.View.extend({

    autoHide: false,
    emptyView: null,
    template: null,

    initialize: function(options){ 
      this.options = options;
      
      this.items = [];
      if (!this.container){
        this.container = this.options.container;
      }
      
      this.template = this.template || this.options.template || _.template('');
      
      this._emptyView = null;
      this.wasEmpty = true;
      
      this.listenTo(this.collection, 'add', this.add);
      this.listenTo(this.collection, 'remove', this.removeItem);
      this.listenTo(this.collection, 'reset', this.update);
      this.listenTo(this.collection, 'sort', this.update);
      
      this.render();
      this.update();
    },
   /**
    * @param item
    */
    add: function(model, collection){
      var index = collection.indexOf(model);
      var itemView = this._createItemView(model, index);
      
      if (this.wasEmpty){
        this.renderContainer();
      }
      
      this._append(this._getContainer(), itemView, index);
      
      itemView.trigger('attach');
      this.items.push({
        id: model.cid,
        view: itemView
      });
      if (this.autoHide && !this.$el.is(':visible'))
        this.$el.show();
    },
    removeItem: function(item){
      var id = item.cid,
          index = -1;
      for (var i = 0; i < this.items.length; i++){
        if (this.items[i].id == id){
          index = i;
          break;
        }
      }
      if (index>=0){
        this.items[index].view.dispose();
        delete this.items[index];
        this.items.splice(index, 1);      
      }
      if (this.isEmpty()){
        this.showEmptyView();
      }
    },
    update: function(){
      var self = this;
      
      this.clear();
      if (this.isEmpty()){
        this.showEmptyView();
      } else {
        this.renderContainer();
      }
      this.collection.each(function(model){
        self.add(model, self.collection);
      });

      this.trigger('update');
    },
    showEmptyView: function(){      
      if ((this.autoHide || this.options.autoHide)){
        this.$el.hide();
        
        return true;
      } else {
        if (this._emptyView || this.emptyView){
          this.wasEmpty = true;
          if (null === this._emptyView){
            this._emptyView = new this.emptyView();
            this._emptyView.$el.addClass('empty-list-notice');
          }
          this._emptyView.setElement(this.$el);
          this._emptyView.render();
          return true;
        }
      }
      return false;
    },
    clear: function(){
      _.each(this.items, function(item){
        item.view.dispose();
      });
      this.items = [];
    },
    render: function(){      
      if (!this.isEmpty()){
        this.renderContainer();
        
        var container = this._getContainer();
        _.each(this.items, function(item){   
          item.view.render();
          this._append(container, item.view);
        }, this);
      } else {
        if (!this.showEmptyView()){
          this.renderContainer();
        }
      }
      
      return this;
    },
    renderContainer: function(){
      this.wasEmpty = false;
      this.$el.html(this.template(this._templateData()));
    },
    _templateData: function(){
      return {};
    },
    _createItemView: function(item, index){
      return new this.itemView({ model: item, index: index });
    },
    _append: function(container, view, index){    
      view.undelegateEvents();
      if (index === 0){
        container.prepend(view.$el);
      } else if (!!index && container.children().length >= index-1){
        view.$el.insertAfter(container.children(':eq('+(index-1)+')'));
      } else {
        container.append(view.$el);
      }
      view.delegateEvents();
    },
    _getContainer: function(){
      var container = this.container ? this.$(this.container) : this.$el;
      if (this.container){
        if (container.length == 0){
          throw new Error("No elements found for selector: " + this.container)
        } else if (container.length > 1){
          throw new Error("More than one element found for selector: " + this.container)
        }
      }
      return container;
    },
    dispose: function(){
      this.stopListening();
      this.clear();
    },
    isEmpty: function(){
      return this.collection.length == 0;
    }
  });
});

