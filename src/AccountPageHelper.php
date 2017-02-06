<?php

namespace Drupal\account_modal;

use Drupal\Core\StringTranslation\StringTranslationTrait;

class AccountPageHelper {
  use StringTranslationTrait;

  public function getPages() {
    return [
      'page' => [
        'label' => $this->t('User page'),
        'routes' => ['user.page'],
        'form' => FALSE,
      ],
      'login' => [
        'label' => $this->t('Login'),
        'routes' => ['user.login'],
        'form' => 'user_login_form',
      ],
      'register' => [
        'label' => $this->t('Register'),
        'routes' => ['user.register'],
        'form' => 'user_register_form',
      ],
      'password' => [
        'label' => $this->t('Password reset'),
        'routes' => ['user.pass'],
        'form' => 'user_pass',
      ],
      'cancel' => [
        'label' => $this->t('Account cancellation'),
        'routes' => ['user.cancel_confirm'],
        'form' => 'user_cancel', // TODO: Verify what this form ID should be
      ],
    ];

    // TODO: Create an event to add pages and one to alter them here.
  }

  public function getPageOptions() {
    $pages = $this->getPages();

    $options = [];

    foreach ($pages as $pageId => $page) {
      $options[$pageId] = $page['label'];
    }

    return $options;
  }

  public function getRoutes($page) {
    $pages = $this->getPages();
    $routes = [];

    if (isset($pages[$page])) {
      $routes = $pages[$page]['routes'];
    }

    return $routes;
  }

  public function getAllRoutes() {
    $pages = $this->getPages();
    $routes = [];

    foreach ($pages as $pageId => $page) {
      $routes += $page['routes'];
    }

    return $routes;
  }

  public function getEnabledPages() {
    $config = \Drupal::config('account_modal.settings');
    $enabledPages = $config->get('enabled_pages');
    $pages = $this->getPages();
    $results = [];

    foreach ($pages as $pageId => $pageInfo) {
      if (in_array($pageId, $enabledPages, TRUE)) {
        $results[$pageId] = $pageInfo;
      }
    }

    return $results;
  }

  public function getPage($pageId) {
    $pages = $this->getPages();

    if (!isset($pages[$pageId])) {
      return NULL;
    }

    return $pages[$pageId];
  }

  public function getPageFromRoute($route) {
    $page = NULL;

    foreach ($this->getEnabledPages() as $pageId => $pageInfo) {
      if (in_array($route, $pageInfo['routes'])) {
        $page = $pageId;

        break;
      }
    }

    return $page;
  }

  public function getPageFromFormId($formId) {
    $page = NULL;

    foreach ($this->getEnabledPages() as $pageId => $pageInfo) {
      if ($formId === $pageInfo['form']) {
        $page = $pageId;

        break;
      }
    }

    return $page;
  }
}
