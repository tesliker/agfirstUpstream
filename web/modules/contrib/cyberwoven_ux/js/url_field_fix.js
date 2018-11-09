(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.cyberwoven_ux_links = {
    attach: function (context) {
      if (context !== document) {
        return;
      }

      function checkURL() {
        var string = this.value;

        if (!~string.indexOf('http')) {
          if(!$(this).prop('required') && !string){
            return this;
          }
          string = 'https://' + string;
        }
        this.value = string;
        return this;
      }

      $('input[type=url]').blur(checkURL);
    }
  };
})(jQuery, Drupal);
