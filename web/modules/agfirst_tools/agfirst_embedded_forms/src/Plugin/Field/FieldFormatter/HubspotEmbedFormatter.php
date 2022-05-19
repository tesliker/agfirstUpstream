<?php

namespace Drupal\agfirst_embedded_forms\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'hubspot_embed_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "hubspot_embed_formatter",
 *   label = @Translation("Hubspot embed formatter"),
 *   field_types = {
 *     "hubspot_embed_field"
 *   }
 * )
 */
class HubspotEmbedFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Todo: Implement settings summary.
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $config = \Drupal::config('agfirst_embedded_forms.configuration');
    $hubspot_link = $config->get('hubspot_location');

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#theme' => 'agfirst_forms_hubspot',
        '#attributes' => [
          'class' => ['hubspot-form-wrapper'],
          'id' => 'hubspot-form-field',
        ],
        '#form_id' => $this->viewValue($item),
        '#hubspot_link' => $hubspot_link,
      ];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    return Html::escape($item->value);
  }

}
