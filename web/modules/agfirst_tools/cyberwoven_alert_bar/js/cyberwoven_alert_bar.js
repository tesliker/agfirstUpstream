(function ($, Drupal) {
  Drupal.behaviors.cyberwovenAlertBar = {
    attach: function (context, settings) {

      var cookieintro = 'cyberwoven_alert_bar_closed_';
      var cyberwoven_alert_bar_closed = $.cookie(cookieintro.concat(settings.cw_alert_bar.unique_id), Number);
      var is_root = (location.pathname === "/");
      if (drupalSettings.alertSettings.homepage_only === 1 && is_root !== true) {
        cyberwoven_alert_bar_closed = 1;
      }

      if (!cyberwoven_alert_bar_closed) {
        if (drupalSettings.alertSettings.enabled) {
          if (drupalSettings.alertSettings.expires === null) {
            $('#cw-alert-bar').addClass('show-alert');
          } else {
            var current_time = Math.round(new Date().getTime() / 1000);
            if (current_time < drupalSettings.alertSettings.expires) {
              $('#cw-alert-bar').addClass('show-alert');
            }
          }
        }
      }

      $('.alert-bar-close', context).on('click', function (e) {
        e.preventDefault();
        if ($('#cw-alert-bar').length > 0) {
          $('#cw-alert-bar').removeClass('show-alert');
          $.cookie(cookieintro.concat(settings.cw_alert_bar.unique_id), 1);
        }
      });

    }
  };
})(jQuery, Drupal);
