define(function(require){
  var ListItemView = require('view/base/list-item-view');

  var DocumentItem = require('model/user/document-item');

  var template = _.template('<span class="custom-input-file custom-input-file_empty">\n' +
    '  <label>\n' +
    '    <input type="file">\n' +
    '  </label>\n' +
    ' <a href="<%=blank%>" class="custom-input-file__title" title="Скачать бланк <%=name%>"><%=name%></a> ' +
    '</span><br>' +
    '<span class="error-list"></span>');

  return ListItemView.extend({
    className: 'document-item',
    events: {
      'change input[type="file"]': 'loadDocument',
    },
    initialize: function(options){
      ListItemView.prototype.initialize.apply(this, arguments);
      this.template = template;
    },
    render: function(){

      this.$el.html(this.template({
        name: this.model.get('name'),
        blank: this.model.get('blank'),
        id: this.model.get('id')
      }));

      return this;
    },
    loadDocument: function (e) {
      var self = this;
      // var file = $('#file1').value;
      // file = file.replace (/\\/g, «/»).split ('/').pop ();
      // // document.getElementById ('file-name').innerHTML = 'Имя файла: ' + file;
      // )
      var file = $(e.currentTarget).val();
      var file_loaded_extension = file.split('.');
      var file_loaded_extension_name = file_loaded_extension[file_loaded_extension.length-1];
      var hiddenInput = e.currentTarget;
      var $customInput =  $(e.currentTarget).parent().parent();
      var model_id = this.model.get('id');
      var file_extension = this.model.get('blank').split('.');
      var file_extension_name = file_extension[file_extension.length-1];
      var reader = new FileReader();

      if (file_extension_name === file_loaded_extension_name) {
        $customInput.removeClass('.custom-input-file_empty').addClass('custom-input-file_load');
        this.$('.error-list').text('');

        reader.readAsDataURL(hiddenInput.files[0]);
        reader.onloadend = function(e){
          var src = e.target.result;
          self.__sendMessage(file, src);

          var message = new DocumentItem({
            id: model_id,
            file: src
          });
          message.on('error', this.onMessageValidateError);
          message.save(message.attributes, {
            success: function (model, response, options) {
              // console.log(model, response, options);
            },
            error: function (model, xhr, options) {
              // console.log(model, xhr, options);
            }
          })
        };
        reader.onerror = function (e) {};

        reader.onabort = function(e) {
          // alert('File read cancelled');
        };
      } else {
        this.validateTypeLoad(file_extension_name);
      }
    },
    validateTypeLoad: function (fileType) {
      this.$('.error-list').text('Загрузите файл с расширением .'+ fileType)
    },
    __sendMessage: function (text, base64) {
      console.log(text, base64);
    },
    onMessageValidateError: function (model, error) {
      console.log(model, error);
    }
  })
});