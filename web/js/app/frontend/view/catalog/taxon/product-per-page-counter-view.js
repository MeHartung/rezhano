/**
 * Created by Dancy on 14.09.2017.
 */
define(function(require){
  var Backbone = require('backbone');

  var template = _.template(require('templates/catalog/taxon/per-page-counters'));

  return Backbone.View.extend({
    className: 'display-number',
    events: {
      'change select': 'onChange'
    },
    initialize: function(){
      this.listenTo(this.model.Pager, 'change', this.render);
    },
    render: function(){
      var p = this.model.Pager.get('pages'),
          per_page = this.model.Pager.get('per_page'),
          page = this.model.Pager.get('page'),
          end = page*per_page,
          nbresults = this.model.Pager.get('nbresults');

      this.$el.html(template({
        counters: [24, 48, 72],
        per_page: per_page,
        start: (page-1)*per_page + 1,
        end: end > nbresults ? nbresults : end,
        total: nbresults,
        url: $.proxy(this.model.url, this.model)
      }));

      return this;
    },
    onChange: function(e){
      var perPage = Number($(e.currentTarget).val());

      this.model.Pager.set('per_page', perPage);

      window.location.href = this.model.url({ count: perPage });
    }
  });
});