/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$
 */
function HTMLUploaderRuntime(options){
//  this.options = $.extend({
//    onSelectFile: null,
//    onRemoveFile: null
//  }, options);
}

(function($){
  var templates = {
    global: '<div class="ig-uploader">\n\
               <form action="" method="POST" enctype="multipart/form-data"\n\
               </form>\n\
             </div>',
    file: '<input type="file" name="" />'
  }
  
  HTMLUploaderRuntime.fn = HTMLUploaderRuntime.prototype;
  
  $.extend(HTMLUploaderRuntime.fn, {
    embed: function(element, url) {
      var self = this;

      var template = $(templates.global);
   
      this.element = element;
      this.element.append(template);
      
      this.form = template.find('form');
      this.form.attr('action', url)
      
      for (var i = 0; i < 3; i++){
        var fileTemplate = $(templates.file).appendTo(this.form)
        fileTemplate.attr('name', 'legacy_multiupload[images_'+i+'][image]')
      }
    },
    submit: function(cb){
      this.form.submit();      
    }
  });
  
})(jQuery)

