/* 
 * Автор Денис Н. Рагозин <dragozin at accurateweb.ru>
 */
(function($){
  function showCitySelect(){
    jQuery('#gkPopupOverlay').css({'visibility':'visible'}).animate({'opacity':0.45},700);    
    jQuery('.city-select-layer').css({'display':'block','height':'auto', 'width':'800px', 'margin-left':'-400px'}).animate({'opacity':1},700);
    jQuery('#cityselection .hint_city').remove();

    return false;
  }
  function hideCitySelect(){
    jQuery('#gkPopupOverlay').css({'visibility':'hidden','opacity':0});
    jQuery('.city-select-layer').css({'display':'none','height':'0px','opacity':0});    
  }
  function saveCitySelect(){
    document.cookie = "city=" + jQuery('#cityselection .cityselect').attr('val')+';path=/';
    jQuery('#cityselection .hint_city').remove();
    return false;
  }
  function city_changed(el){
    hideCitySelect();

    document.cookie = "city=" + jQuery(el).data('id')+';path=/';
    window.location.reload();        

    return false;
  }
  jQuery(document).ready(function(){
    setTimeout(__setListenters, 1000)
  });
  function __setListenters(){
    if(!jQuery('#gkPopupOverlay') && !jQuery('#layer__close')){
      setTimeout(__setListenters, 1000);
      return;
    }
    jQuery('#gkPopupOverlay').click(function(){ hideCitySelect(); });
    jQuery('#cityselection .hint_city .hint_button_close').click(function(){ return  saveCitySelect(); });
    jQuery('#cityselection .hint_city .hint_change_city').click(function(){ return  showCitySelect(); });
    //jQuery('#cityselection .cityselect').click(function(){ return  showCitySelect(); });

    $('.list-stores').on('click', '.city-select-link', onCitySelectLinkClick);
    $('.region-select').on('change', 'select', onRegionSelectChange);
    $('.city-select-layer').on('click', '#gkclose, .button-close', function(){ hideCitySelect(); });
  }
  
  function onCitySelectLinkClick(e){
    e.preventDefault();
    
    city_changed(this);
  }

  function onRegionSelectChange(e){
    var region = $(e.target).val(),
        $loader = $('.region-select .ajax-loader').show();
        
    $('.list-stores').html('');    
    $.getJSON('/app_dev.php/api/geography/cities?region='+encodeURIComponent(region), function(r){
      var $listHtml = '';
      for (var i = 0; i < r.length; i++){
        $listHtml += '<li><a class="city-select-link'+(r[i].selected ? ' checked' : '')+'" href="#" data-id="'+r[i].code+'">'+r[i].name+'</a></li>';
      }
      $('.list-stores').html($listHtml);
    }).always(function(){
      $loader.hide();
    });
  }
  
})(jQuery);