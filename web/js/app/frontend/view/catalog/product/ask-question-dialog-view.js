/**
 * Created by Денис on 24.08.2017.
 */
define(function(require) {
  var ModalDialog = require('view/dialog/base/modal-dialog-view'),
      ProductQuestion = require('model/catalog/product/product-question');

  require('backbone-forms');

  var dialogTemplate = _.template('\
<div class="layer__close"></div>\
<div class="layer__in">\
  <div>\
    <h2>Задать вопрос о товаре</h2>\
    <div class="product-summary">\
    <div class="width70 floatleft">\
      <h3><%= product_name %></h3>\
      <div class="short-description"><%= product_description %></div>\
    </div>\
    <% if (product_image) { %>\
    <div class="width30 floatleft center">\
      <img src="<%= product_image %>" alt="<%= product_name %>" class="product-image" style="max-width: 150px;">		\
    </div>\
    <% } %>\
    <div class="clear"></div>\
  </div>\
  <div class="form-field">\
    <form class="form-validate" action="" method="post">\
      <div class="inputs"></div>\
      <div class="submit">\
        <input class="highlight-button" type="submit" name="submit_ask" title="Отправить вопрос" value="Отправить вопрос">\
      </div>\
		</form>\
  </div>    \
</div>\
');

  return ModalDialog.extend({
    events: _.extend({}, ModalDialog.events, {
      'click .layer__close': 'onCloseButtonClick',
      'submit form': 'onFormSubmit'
    }),
    template: dialogTemplate,
    initialize: function(options){
      this.product = options.product;

      ModalDialog.prototype.initialize.apply(this, arguments);

      this.model = new ProductQuestion({}, {
        slug: this.product.get('slug')
      });

      this.form = new Backbone.Form({
        model: this.model
      })
    },
    render: function(){
      this.$el.html(this.template({
        product_name: this.product.get('name'),
        product_description: this.product.get('description_short'),
        product_image: this.product.get('image')
      }));

      this.form.
           render().$el.appendTo(this.$('.inputs'));

      return this;
    },
    onFormSubmit: function(e){
      e.preventDefault();

      var self = this;

      var errors = this.form.commit();
       if (!errors){
         this.model.save(null, {
           success: function(){
             self.$('.form-field').html('Ваш вопрос успешно отправлен. Мы свяжемся с Вами в ближайшее время');
           },
           error: function(){
             self.$('.form-field').html('При отправке запроса произошла ошибка. Пожалуйста, свяжитесь с нами другим способом или попробуйте отправить вопрос позднее.');
           }
         });
       }
    }
  });
});