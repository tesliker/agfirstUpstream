<?php

/**
 * @file
 * Contains \Drupal\cyberwoven_alert_bar\Form\CyberwovenAlertBarModuleSettingsForm
 */
namespace Drupal\cyberwoven_alert_bar\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;

/**
 * Configure cyberwoven_alert_bar settings for this site.
 */
class CyberwovenAlertBarModuleSettingsForm extends ConfigFormBase {

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

    $config = $this->config('cyberwoven_alert_bar.module_settings');

    $form['cyberwoven_alert_bar_toggle_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Toggle Settings'),
      '#open' => TRUE,
    ];

    $form['cyberwoven_alert_bar_toggle_settings']['cyberwoven_alert_bar_toggle_severity'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable severity selection.'),
      '#description' => $this->t('When checked, the "Severity" select box on the <em>Manage alert</em> page will not be available.'),
      '#default_value' => $config->get('hide_severity'),
    ];

    $form['cyberwoven_alert_bar_toggle_settings']['cyberwoven_alert_bar_toggle_additional_settings'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable the Additional Settings configuration.'),
      '#description' => $this->t('When checked, the "Additional Settings" section of the <em>Manage alert</em> page will not be available.'),
      '#default_value' => $config->get('hide_additional_settings'),
    ];

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


    /**
     * 'severity' => ($module_config->get('hide_severity')) ? null : $config->get('severity'),
     * 'alert_color_scheme' => ($module_config->get('hide_additional_settings')) ? null : $config->get('alert_color_scheme'),
     * 'additional_classes' => ($module_config->get('hide_additional_settings')) ? null : $config->get('additional_classes'),
     */

    $config = \Drupal::service('config.factory')->getEditable('cyberwoven_alert_bar.module_settings');
    $config
      ->set('hide_severity', $form_state->getValue('cyberwoven_alert_bar_toggle_severity'))
      ->set('hide_additional_settings', $form_state->getValue('cyberwoven_alert_bar_toggle_additional_settings'))
      ->save();

    // Nullify the alert settings that are hidden.
    $config = \Drupal::service('config.factory')->getEditable('cyberwoven_alert_bar.settings');

    if ($form_state->getValue('cyberwoven_alert_bar_toggle_severity')) {
      $config->set('severity', NULL)->save();
    }

    if ($form_state->getValue('cyberwoven_alert_bar_toggle_additional_settings')) {
      $config
        ->set('alert_color_scheme', NULL)
        ->set('additional_classes', NULL)
        ->save();
    }

    Cache::invalidateTags(array('config:cyberwoven_alert_bar.module_settings'));

  }
}
