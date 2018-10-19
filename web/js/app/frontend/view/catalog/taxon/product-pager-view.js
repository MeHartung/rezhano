/**
 * Created by Dancy on 14.09.2017.
 */
define(function(require){
  var Backbone = require('backbone');

  var template = _.template(require('templates/catalog/taxon/pagination'));

  return Backbone.View.extend({
    initialize: function() {
      $(document).on('keydown', $.proxy(this.onKeyDown, this));

      this.model.on("change", this.render, this);
    },
    render: function(){
      this.$el.html(template(this.model.toJSON()));
    },
    onKeyDown: function (ev){
      var self = this,
          current = this.model.get('pages').current,
          links   =  this.model.get('pages').links,
          pager = this.model.get('pages');

      ev = (ev) ? ev : ((window.event) ? event : null);
      if (ev) {
        if (ev.ctrlKey && (ev.keyCode == 39)) {
          if (pager.page < pager.page_count) {
            window.location.href = links[current+1];
          }
        }
        if (ev.ctrlKey && (ev.keyCode == 37)) {
          if (pager.page > 1) {
            window.location.href = links[current-1];
          }
        }
      }
    }
  });
});