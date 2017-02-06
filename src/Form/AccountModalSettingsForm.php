<?php

namespace Drupal\account_modal\Form;

use Drupal\account_modal\AccountPageHelper;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * The admin settings form for Account Modal.
 */
class AccountModalSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'account_modal_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('account_modal.settings')
      ->set('enabled_pages', $form_state->getValue('enabled_pages'))
      ->set('reload_on_success', $form_state->getValue('reload_on_success'))
      ->set('create_profile_after_registration', $form_state->getValue('create_profile_after_registration'))
      ->set('profile_type', $form_state->getValue('profile_type'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['account_modal.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('account_modal.settings');

    $accountPageHelper = new AccountPageHelper();

    $form['enabled_pages'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enabled pages'),
      '#description' => $this->t('Select the account pages to show in a modal window.'),
      '#options' => $accountPageHelper->getPageOptions(),
      '#default_value' => $config->get('enabled_pages'),
    ];

    $form['reload_on_success'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Reload on success'),
      '#description' => $this->t('Reload the page (instead of redirecting) upon completion.'),
      '#default_value' => $config->get('reload_on_success'),
    ];

    /** @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler */
    $moduleHandler = \Drupal::service('module_handler');
    $profileIsInstalled = $moduleHandler->moduleExists('profile');

    $form['create_profile_after_registration'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Create profile after registration'),
      '#description' => $this->t('Optionally, show a form to create a new profile after registration. Requires the Profile module.'),
      '#disabled' => !$profileIsInstalled,
      '#default_value' => $profileIsInstalled ? $config->get('create_profile_after_registration') : FALSE,
    ];

    $form['profile_type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Profile type'),
      '#description' => $this->t('If creating a profile, enter the bundle to create.'),
      '#disabled' => !$profileIsInstalled,
      '#default_value' => $config->get('profile_type'),
    ];

    return parent::buildForm($form, $form_state);
  }
}
