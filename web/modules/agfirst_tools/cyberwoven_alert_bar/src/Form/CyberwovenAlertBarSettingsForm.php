<?php

/**
 * @file
 * Contains \Drupal\cyberwoven_alert_bar\Form\CyberwovenAlertBarSettingsForm
 */
namespace Drupal\cyberwoven_alert_bar\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;

/**
 * Configure cyberwoven_alert_bar settings for this site.
 */
class CyberwovenAlertBarSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cyberwoven_alert_bar_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'cyberwoven_alert_bar.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('cyberwoven_alert_bar.settings');
    $module_config = $this->config('cyberwoven_alert_bar.module_settings');

    $form['cyberwoven_alert_bar_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show the alert message.'),
      '#default_value' => $config->get('enabled'),
    ];

    $form['cyberwoven_alert_bar_homepage_only'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Limit the alert to the homepage.'),
      '#default_value' => $config->get('homepage_only'),
    ];

    $form['cyberwoven_alert_bar_expires'] = [
      '#type' => 'date',
      '#title' => $this->t('Expiration'),
      '#default_value' => $config->get('expires'),
      '#description' => $this->t(/** @lang text */"The expiration date is the last day the alert will be displayed.<br>If no expiration is set, the alert will continue to display until \"@enabled_title\" is unchecked.", array(
        '@enabled_title' => $form['cyberwoven_alert_bar_enabled']['#title'],
      )),
    ];

    if ($module_config->get('hide_severity') !== 1) {

      $severity = $config->get('severity');
      $form['cyberwoven_alert_bar_severity'] = [
        '#type' => 'select',
        '#title' => $this->t('Severity'),
        '#required' => true,
        '#options' => [
          'message' => $this->t('Message'),
          'warning' => $this->t('Warning'),
          'critical' => $this->t('Critical'),
        ],
        '#default_value' => (($severity) ? $severity : 'message'),
      ];

    }

    $form['cyberwoven_alert_bar_message'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Message'),
      '#required' => true,
      '#format' => $config->get('message')['format'],
      '#default_value' => $config->get('message')['value'],
    ];

    $form['cyberwoven_alert_bar_more_link'] = [
      '#type' => 'details',
      '#title' => $this->t('More link'),
      '#open' => TRUE,
    ];

    $form['cyberwoven_alert_bar_more_link']['cyberwoven_alert_bar_more_link_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link label'),
      '#placeholder' => $this->t('Link label'),
      '#description' => $this->t('If a URL is provided and this field is left blank, the label will default to "Learn More".'),
      '#default_value' => $config->get('more_link_label'),
    ];

    $form['cyberwoven_alert_bar_more_link']['cyberwoven_alert_bar_more_link_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Link URL'),
      '#placeholder' => $this->t('Link URL'),
      '#description' => $this->t(/** @lang html */'If this field is left blank, the link will not appear. <b>Note:</b> Only full URLs are accepted.'),
      '#maxlength' => 512,
      '#default_value' => $config->get('more_link_url'),
    ];

    $form['cyberwoven_alert_bar_more_link']['cyberwoven_alert_bar_more_link_external'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Open link in a new browser.'),
      '#default_value' => $config->get('more_link_external'),
    ];

    if ($module_config->get('hide_additional_settings') !== 1) {
      $form['cyberwoven_alert_bar_alert_settings'] = [
        '#type' => 'details',
        '#title' => $this->t('Additional Settings'),
        '#open' => TRUE,
      ];

      $color_scheme = $config->get('alert_color_scheme');
      $form['cyberwoven_alert_bar_alert_settings']['cyberwoven_alert_bar_alert_color_scheme'] = [
        '#type' => 'select',
        '#title' => $this->t('Color scheme'),
        '#required' => true,
        '#options' => [
          'scheme-default' => $this->t('Default'),
          'scheme-alternate' => $this->t('Alternate'),
        ],
        '#description' => $this->t('Select between "Default" and "Alternate" color schemes for the alert.'),
        '#default_value' => (($color_scheme) ? $color_scheme : 'alternate'),
      ];

      $form['cyberwoven_alert_bar_alert_settings']['cyberwoven_alert_bar_additional_classes'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Additional classes'),
        '#description' => $this->t('Provide additional CSS classes to the alert. Separate multiple classes with spaces.'),
        '#default_value' => $config->get('additional_classes'),
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // $config = $this->config('cyberwoven_alert_bar.settings');
    $config = \Drupal::service('config.factory')->getEditable('cyberwoven_alert_bar.settings');
    $config
      ->set('enabled', $form_state->getValue('cyberwoven_alert_bar_enabled'))
      ->set('expires', $form_state->getValue('cyberwoven_alert_bar_expires'))
      ->set('severity', $form_state->getValue('cyberwoven_alert_bar_severity'))
      ->set('homepage_only', $form_state->getValue('cyberwoven_alert_bar_homepage_only'))
      ->set('message', $form_state->getValue('cyberwoven_alert_bar_message'))
      ->set('more_link_label', $form_state->getValue('cyberwoven_alert_bar_more_link_label'))
      ->set('more_link_url', $form_state->getValue('cyberwoven_alert_bar_more_link_url'))
      ->set('more_link_external', $form_state->getValue('cyberwoven_alert_bar_more_link_external'))
      ->set('alert_color_scheme', $form_state->getValue('cyberwoven_alert_bar_alert_color_scheme'))
      ->set('additional_classes', $form_state->getValue('cyberwoven_alert_bar_additional_classes'))
      ->set('unique_id', uniqid())
      ->save();

    Cache::invalidateTags(array('config:cyberwoven_alert_bar.settings'));

  }
}
