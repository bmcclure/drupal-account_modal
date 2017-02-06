<?php

namespace Drupal\account_modal;

use Drupal\account_modal\AjaxCommand\RefreshPageCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormStateInterface;

class AccountModalAjaxHelper {
  public static function ajaxCallback($pageId, array $form, FormStateInterface $formState) {
    $response = new AjaxResponse();
    $messages = drupal_get_messages(NULL, FALSE);

    if (!isset($messages['error'])) {
      $response->addCommand(new CloseDialogCommand());

      switch ($pageId) {
        case 'login':
          drupal_set_message(t('You have been successfully logged in. Please wait a moment.'));
          $response->addCommand(self::redirectCommand($formState));

          break;
        case 'register':
          drupal_set_message(t('You have successfully created an account. Please wait a moment.'));
          $response->addCommand(self::redirectCommand($formState));

          break;
      }
    }

    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = \Drupal::service('renderer');
    $messagesElement = ['#type' => 'status_messages'];

    $response->addCommand(new AppendCommand(
      '#account_modal_' . $pageId . '_wrapper',
      $renderer->renderRoot($messagesElement)
    ));

    return $response;
  }

  public static function redirectCommand(FormStateInterface $formState) {
    global $base_url;

    $config = \Drupal::config('account_modal.settings');

    return ($config->get('prevent_redirect'))
      ? new RefreshPageCommand()
      : new RedirectCommand($base_url . $formState->getRedirect());
  }
}
