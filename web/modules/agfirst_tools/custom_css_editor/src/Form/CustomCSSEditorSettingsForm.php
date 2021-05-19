<?php

/**
 * @file
 * Contains \Drupal\custom_css_editor\Form\CustomCSSEditorSettingsForm
 */
namespace Drupal\custom_css_editor\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;

/**
 * Configure custom_css_editor settings for this site.
 */
class CustomCSSEditorSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_css_editor_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'custom_css_editor.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('custom_css_editor.module_settings');

    $form['custom_css_editor_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Toggle Content Types'),
      '#open' => TRUE,
    ];

    $options = [];

    $node_types = \Drupal::entityTypeManager()
      ->getStorage('node_type')
      ->loadMultiple();

    foreach ($node_types as $type) {
      $options[$type->id()] = $type->label();
    }

    $form['custom_css_editor_settings']['bundles'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Content Types'),
      '#options' => $options,
      '#default_value' => $config->get('bundles') ?: [],
      '#description' => $this->t('Select the content types on which to enable the CSS editor.'),
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

    $config = \Drupal::service('config.factory')->getEditable('custom_css_editor.module_settings');
    $config
      ->set('bundles', $form_state->getValue('bundles'))
      ->save();

    Cache::invalidateTags(array('config:custom_css_editor.module_settings'));

  }
}
