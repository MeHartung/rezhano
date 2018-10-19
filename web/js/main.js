// /*
//  * @author Alexander Grinevich <agrinevich at accurateweb.ru>
//  */
// $(function () {
//
//

  // $('#datePicker input').daterange({
  //   dateFormat: "dd.mm.yy"
  // });
  // $('.custom-border-select select').selectmenu();

//
//
//   $(".password-input-wrap__toggle").click(function() {
//     $(this).toggleClass("visible");
//     var input = $(this).parent().find('input');
//     if (input.attr("type") == "password") {
//       input.attr("type", "text");
//       $(this).attr('title', 'Скрыть пароль');
//     } else {
//       input.attr("type", "password");
//       $(this).attr('title', 'Показать пароль');
//     }
//   });
//
//   $('#openMenu1').on('click', function () {
//     $('#expandedMenu1').css({'display': 'block'})
//   });
//   $('#closeMenu1').on('click', function () {
//     $('#expandedMenu1').css({'display': 'none'})
//   });
//   $('#sliderMain').slick({
//     infinite: true,
//     dots: true,
//     arrows: true,
//     autoplay: true,
//     autoplayTime: 2000,
//     slidesToShow: 2,
//     slidesToScroll: 2
//   });
//   // $('#datePicker').datepicker();
//   $( "#tabs" ).tabs();
//   $( "#tabsDescription" ).tabs();
//   $( "#registerTabs" ).tabs();
//   $( "#deliveryTabs" ).tabs();
//   $( "#cabinetTabs" ).tabs();
//
//   $(document)
//     .on('click', '.layer__close', function (e) {
//       e.preventDefault();
//       closeLayer();
//     })
//     .on('click', '.js-sign', function (e) {
//       e.preventDefault();
//       openUserLayer();
//     })
//     .on('click', '.js-cart', function (e) {
//       e.preventDefault();
//       openCartLayer();
//     })
//     .on('click', '.js-restore', function (e) {
//       e.preventDefault();
//       $('.user-layer').hide();
//       $('.restore-layer').show();
//       $('.ui-widget-overlay').fadeIn('fast');
//     })
//     .on('click', '.js-messages', function (e) {
//       e.preventDefault();
//       $('.notice-popup').toggle();
//     })
//     .mouseup(function (e){
//       var div = $('.layer');
//       if (!div.is(e.target)
//         && div.has(e.target).length === 0) {
//         closeLayer();
//       }
//     });
// });
// function openCartLayer() {
//   $('.add-to-cart-layer').show();
//   $('.ui-widget-overlay').fadeIn('fast');
// }
//
// function closeLayer () {
//   $('.layer').hide();
//   $('.ui-widget-overlay').fadeOut('fast');
// }
//
// function openUserLayer () {
//   $('.user-layer').show();
//   $('.ui-widget-overlay').fadeIn('fast');
// }
//
// function getFileName (value) {
//   var file = $('#file1').value;
//   // file = file.replace (/\\/g, «/»).split ('/').pop ();
//   // // document.getElementById ('file-name').innerHTML = 'Имя файла: ' + file;
//   // )
//
//   var $input = value;
//   var $customInput = $($input).parent().parent();
//
//   $customInput.removeClass('.custom-input-file_empty').addClass('custom-input-file_load');
//
//
// }
// //
// $(function () {
  //
  // $('.product-item__title').ellipsis({
  //   position: 'tail',
  //   row: 2
  // });

  // $('.scroll-pane').jScrollPane();
  //
  // $(".custom-datepicker").daterange({
  //   onClose: function (dateRangeText) {
  //     $(".custom-datepicker").after("<p>" + dateRangeText + "</p>");
  //   }
  // });
// //   $('.custom-border-select select').selectmenu();
// });

