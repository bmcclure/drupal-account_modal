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
      ->set('prevent_redirect', $form_state->getValue('prevent_redirect'))
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

    $form['prevent_redirect'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Prevent redirect'),
      '#description' => $this->t('Reload the page instead of redirecting upon form submission.'),
      '#default_value' => $config->get('prevent_redirect'),
    ];

    return parent::buildForm($form, $form_state);
  }
}
