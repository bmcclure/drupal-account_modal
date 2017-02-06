(function ($, Drupal, drupalSettings, window) {
  'use strict';

  Drupal.behaviors.accountModal = {
    attach: function (context) {
      Drupal.AjaxCommands.prototype.accountModalRefreshPage = function (ajax, response) {
        window.location.reload();
      };
    }
  };

}(jQuery, Drupal, drupalSettings, window));
