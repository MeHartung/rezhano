/*
 * @author Alexander Grinevich <agrinevich at accurateweb.ru>
 */

$(function () {
  $('#status_admin_reasonChoice').change(function () {
    fillNote();
  });
});

function fillNote() {
  var note = $('#status_admin_reasonChoice').val();
  $('#status_admin_reason').val(note);
}