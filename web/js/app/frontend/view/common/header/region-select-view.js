/**
 * Created by Dancy on 15.09.2017.
 */
define(function(require){
  var Backbone = require('backbone');

  var template = _.template('\
 Область: <select>\
        <% regions.each(function(region){ %>\
          <option value="<%= region.get(\'name\') %>"<% if (region.get(\'name\') == selectedRegion) { %> selected="selected"<% } %>><%= region.get(\'name\') %></option>\
        <% }) %>\
        </select>\
        <img class="ajax-loader" src="/images/ajax-loader.gif" alt="Загрузка..." title="Загрузка..." style="display:none;"/>\
');

  return Backbone.View.extend({
    events: {
      'change select': 'onRegionSelectChange'
    },
    initialize: function(options){
      this.location = options.location;
    },
    render: function(){
      this.$el.html(template({
        regions: this.collection,
        selectedRegion: this.location.city.get('region')
      }));

      return this;
    },
    onRegionSelectChange: function(){
      this.trigger('change', this.$('select').val());
    }
  });
});