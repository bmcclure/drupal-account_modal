<?php

namespace Drupal\account_modal;

use Drupal\account_modal\AjaxCommand\RefreshPageCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\profile\Entity\Profile;

/**
 * A helper class for creating Ajax responses for Account Modal.
 */
class AccountModalAjaxHelper {
  public static function ajaxCallback($pageId, array $form, FormStateInterface $formState) {
    $response = new AjaxResponse();
    $messages = drupal_get_messages(NULL, FALSE);

    if (!isset($messages['error'])) {
      $response->addCommand(new CloseModalDialogCommand());

      switch ($pageId) {
        case 'login':
          drupal_set_message(t('You have been successfully logged in. Please wait a moment.'));
          $response->addCommand(self::redirectCommand($formState));

          break;
        case 'register':
          drupal_set_message(t('You have successfully created an account. Please wait a moment.'));

          $config = \Drupal::config('account_modal.settings');

          /** @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler */
          $moduleHandler = \Drupal::service('module_handler');
          $profileIsInstalled = $moduleHandler->moduleExists('profile');

          if ($config->get('create_profile_after_registration') && $profileIsInstalled) {
            $response->addCommand(self::newProfileCommand($formState));
          } else {
            $response->addCommand(self::redirectCommand($formState));
          }

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

    return ($config->get('reload_on_success'))
      ? new RefreshPageCommand()
      : new RedirectCommand($base_url . $formState->getRedirect());
  }

  public static function newProfileCommand(FormStateInterface $formState) {
    $config = \Drupal::config('account_modal.settings');

    $profileBundle = $config->get('profile_bundle') ?: 'customer';

    $profile = \Drupal::entityTypeManager()->getStorage('profile')->create([
      'type' => $profileBundle,
      'uid' => \Drupal::currentUser()->id(),
      'status' => TRUE,
      'is_default' => TRUE,
    ]);

    /** @var \Drupal\Core\Entity\EntityFormBuilderInterface $entityFormBuilder */
    $entityFormBuilder = \Drupal::service('entity.form_builder');
    $form = $entityFormBuilder->getForm($profile);

    return new OpenModalDialogCommand('Create a Profile', $form);
  }

  public static function hideFieldDescriptions(array &$form) {
    foreach ($form as $key => $element) {
      if (strpos($key, '#') === 0 || !is_array($element)) {
        continue; // Skip non-fields or things that aren't arrays
      }

      unset($form[$key]['#description']);

      // Now hide any child field descriptions.
      self::hideFieldDescriptions($form[$key]);
    }
  }
}
