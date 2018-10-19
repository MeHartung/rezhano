function tinymce_button_quote(ed) {
  var sel = ed.selection.getSel();
  val = '';

  if (sel.type == 'Range') {
    val = ed.selection.getContent();
  }

  ed.windowManager.open({
    title: "Текст интервью",
    body: [{
      type: "textbox",
      name: "text",
      label: "Текст цитаты",
      multiline: true,
      minWidth: 600,
      minHeight: 60,
      value: val
    }],
    onsubmit: function (e) {
      ed.insertContent('<div class="incoming-message__quote">' + e.data.text + "</div>")
    }
  });
}