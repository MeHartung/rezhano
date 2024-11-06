/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$ 
 */
define(function(require){
  var Backbone = require('backbone');

  require('prettyphoto');

  return Backbone.View.extend({
    events: {
      'click .product-image': 'onSliderItemClick',
      'click .main-image a': 'onMainImageClick'
    },
    initialize: function(){
      this.ppDefaults = {
        images: [],
        titles: [],
        descriptions: []
      }
    },
    render: function(){
      var self = this;

      if (this.$('.product-image').length) {
        this.$('.product-image').each(function(){
          self.ppDefaults.images.push($(this).attr('href'));
          self.ppDefaults.titles.push($(this).attr('title'));
          self.ppDefaults.descriptions.push('');
        });
      } else {
        var $mainImage = this.$('.main-image');
        if ($mainImage.length){
          var $a = $mainImage.find('a');
          self.ppDefaults.images.push($a.attr('href'));
          self.ppDefaults.titles.push($a.attr('title'));
          self.ppDefaults.descriptions.push('');
        }
      }
      $.fn.prettyPhoto({
        social_tools: false,
        deeplinking: false,
        keyboard_shortcuts: false
      });

      this.$('.product-image__gallery__additional').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        variableWidth: true,
      });

      return this;
    },
    onSliderItemClick: function(e){
      var lnk = $(e.currentTarget);

      e.preventDefault();
      
      this.$('.slider-item.active').removeClass('active');
      
      this.$('.main-image img').attr('src', lnk.attr('href'));
      this.$('.main-image a').attr('href', lnk.attr('href'));
      
      lnk.parent().addClass('active');      
    },
    onMainImageClick: function(e){
      e.preventDefault();

        /**
         * Находим ключ пикчи. Теперь слайдшоу начнётся с неё.
         * @type {*}
         */
      var imgId = this.ppDefaults.images.indexOf(this.$('.main-image a').attr('href'));
      var imageHref = [];
      imageHref.push(e.currentTarget.href);

      if (this.ppDefaults.images.length){
        if (this.$('.main-image').hasClass('slick-slider')) {
          $.prettyPhoto.open(imageHref, this.ppDefaults.titles, this.ppDefaults.descriptions);
        } else {
          $.prettyPhoto.open(this.ppDefaults.images, this.ppDefaults.titles, this.ppDefaults.descriptions);
        }

        $.prettyPhoto.changePage(imgId);
        $('.pp_gallery').addClass('disabled');
      }
    }
  });
});

