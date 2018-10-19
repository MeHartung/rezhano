/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone');
  
  var template = _.template('\n\
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>При оформлении заказа произошла непредвиденная ошибка. Приносим свои извинения за доставленные неудобства.</p>\n\
<p>Вот, что можно попробовать сделать:</p>\n\
<ol>\n\
  <li><a class="dashed try-again" href="#">Попробуйте еще раз</a></li>\n\
  <li>Позвоните нам по любому из телефонов, указанных в <a id="scroll-up" href="#" class="dashed">верхней части страницы</a>, и наш сотрудник оформит заказ для Вас</li>\n\
  '+/*<li>Оставьте <a class="checkout-recall-request dashed" href="#">заявку на обратный звонок</a>, и наш сотрудник самостоятельно Вам перезвонит</a></li>*/'\n\
</ol>');
  
  return Backbone.View.extend({
    className: 'checkout-error-dialog',
    events: {
      'click .try-again': 'onTryAgainClick',
      'click .checkout-recall-request': 'onCheckoutRecallRequestClick',
      'click #scroll-up': 'onScrollUpClick',
      'hide *': 'onHide'
    },
    initialize: function(options){
      this.options = options;
      
      var self = this;
      
      this.$el.dialog({
        autoOpen: false,
        modal: true,
        title: 'Ошибка оформления заказа',
        resizable: false,
        minWidth: 450,
        buttons: {
          'Закрыть': function(){
            self.$el.dialog('close');
          }
        },
        close: function(){
          self.$el.dialog('destroy');
          self.dispose();
        }
      });
    },
    open: function(){
      this.render();
      this.$el.dialog('open');
    },
    render: function(){
      this.$el.html(template({}))
      
      return this;
    },
    onTryAgainClick: function(e){
      e.preventDefault();
      
      this.options.form.submit();      
      this.$el.dialog('destroy');
      this.dispose();
    },
    onCheckoutRecallRequestClick: function(e){
      e.preventDefault();      
      
      this.$el.dialog('destroy');      
      this.dispose();
    },
    onScrollUpClick: function(e){
      e.preventDefault();
      
      $('body,html').animate({
          scrollTop: 0
      }, 400);    
      
      //предотвратим всплытие события, чтобы избежать пропадания модального слоя
      return false;
    },
    onHide: function(e){
      e.preventDefault();
      e.isModal = true;
    },
    dispose: function(){
      this.stopListening();
      this.$el.remove();
    }
  });
});

