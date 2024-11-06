define(function(require){
  var Backbone = require('backbone'),
      ProfileView = require('view/user/profile-view');

  return ProfileView.extend({
    initialize: function(options){
      ProfileView.prototype.initialize.apply(this, [options]);
    },
    render: function(){
      ProfileView.prototype.render.apply(this);
      this.$('#fos_user_profile_form_phone').inputmask('+7 (999) 999-99-99');
    }
  });
});