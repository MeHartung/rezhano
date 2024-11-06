define(function(require){
   var Backbone = require('backbone'),
       PasswordInputWidget = require('view/widget/password-input-widget');

   return Backbone.View.extend({
       initialize: function(){
           this.widgets = [
               new PasswordInputWidget({
                   el: $('#fos_user_resetting_form_plainPassword_first').parent()
               }),
               new PasswordInputWidget({
                   el: $('#fos_user_resetting_form_plainPassword_second').parent()
               })
           ]
       },
       render: function () {
           _.each(this.widgets, function(widget){
               widget.render();
           });

           return this;
       }
   })
});