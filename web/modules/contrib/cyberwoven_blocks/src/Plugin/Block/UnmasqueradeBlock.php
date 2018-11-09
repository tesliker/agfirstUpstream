<?php

namespace Drupal\cyberwoven_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a 'unmasquerade_block' block.
 *
 * @Block(
 *   id = "cyberwoven_unmasquerade_block",
 *   admin_label = @Translation("Cyberwoven providede 'Unmasquerade' block"),
 *   category = @Translation("Cyberwoven providede Unmasquerade block")
 * )
 */
class UnmasqueradeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = [];

    if (\Drupal::service('masquerade')->isMasquerading()) {
      $form['masquerade_switch_back'] = [
        '#type' => 'link',
        '#title' => 'Unmasquerade',
        '#url' => Url::fromRoute('masquerade.unmasquerade'),
      ];
    }

    return $form;
  }
}
