define(function(require){
   var Backbone = require('backbone');

   var template = _.template('' +
       '<div class="layer__title">Восстановление пароля</div>\n' +
       '      <form method="POST" class="layer__form" action="<%= urlPrefix %>/passwordreset/send-email">\n' +
       '        <ul class="error-list global"></ul>' +
       '        <input class="layer__input" type="text" placeholder="Почта" name="username">\n' +
       '        <div class="user-layer__controls">\n' +
       '          <span class="hint">\n' +
       '            На указанную почту будет<br>\n' +
       '            отправлено письмо со ссылкой<br>\n' +
       '            для смены пароля\n' +
       '          </span>\n' +
       '        </div>\n' +
       '        <div class="buttons">\n' +
       '          <button class="button layer__button">Восстановить пароль</button>\n' +
       '        </div>\n' +
       '      </form>\n'),
       successTemplate = _.template('<div class="layer__title">Восстановление пароля</div>\n' +
           '      <form method="POST" class="layer__form" action="<%= urlPrefix %>/passwordreset/send-email">\n' +
           '        <div class="user-layer__controls">\n' +
           '          <span class="hint">\n' +
           '            Письмо с инструкциями по смене пароля отправлено на указанный адрес E-mail\n' +
           '          </span>\n' +
           '        </div>\n' +
           '      </form>\n')
       ;

   return Backbone.View.extend({
       events: {
           'submit form': 'onSubmit'
       },
       render: function() {
           var self = this;

           this.$el.html(template());

           return this;
       },
       onSubmit: function(e){
        e.preventDefault();
        var self = this;
        var $form = this.$('form');

        $form.addClass('loading');

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: "json",
            success: function(data) {
                if (data.success) {
                    // Обновляем страницу
                    self.$el.html(successTemplate());
                }
            },
            error: function(data){
                for (var field in data.responseJSON) {
                    var $field = $form.find('[name*='+field+']');
                    if (data.responseJSON.hasOwnProperty(field)) {
                        if ($field.length) {
                            $field.parent('.form-group').addClass('invalid').find('.error-list').html('<div>' + data.responseJSON[field]+ '</div>');
                        } else {
                            $form.find('.error-list.global').html('<li>'+ data.responseJSON[field] + '</li>');
                        }
                    }
                }
            },
            complete: function()
            {
                $form.removeClass('loading')
            }
        });
    }
   });
});