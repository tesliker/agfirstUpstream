/**
 * @file
 * Custom CSS Editor module.
 */

(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.customCssEditor = {
    attach: function (context, settings) {
      if (typeof ace == 'undefined' || typeof ace.edit != 'function') {
        return;
      }

      $('.ace-editor').once('ace-editor-added').each(function () {
        let textarea = $(this).parent().siblings().find('textarea');

        $(textarea).css('position', 'absolute')
          .css('width', "1px")
          .css('height', "1px")
          .css('opacity', 0)
          .attr('tabindex', -1);

        let editor = ace.edit(this);
        editor.getSession().setMode('ace/mode/css');
        editor.getSession().setTabSize(2);

        editor.getSession().on('change', function () {
          textarea.val(editor.getSession().getValue());
        });

        $('.resizable').resizable({
          resize: function (event, ui) {
            editor.resize();
          }
        });

        editor.setValue(textarea.val(), -1);
        editor.resize();

        // When the form fails to validate because the text area is required,
        // shift the focus to the editor.
        textarea.on('focus', function () {
          editor.textInput.focus()
        })

        editor .clearSelection();
      });
    }
  };
})(jQuery, Drupal);
