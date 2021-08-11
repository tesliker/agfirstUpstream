<?php

namespace Drupal\agfirst_financial_calculators\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'PatronageCalculator' block.
 *
 * @Block(
 *  id = "patronage_calculator",
 *  admin_label = @Translation("Patronage Calculator"),
 * )
 */
class PatronageCalculator extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $config = \Drupal::config('agfirst_financial_calculators.settings');

    $build['patronage_calculator'] = [
      '#theme' => 'agfirst_financial_calculators_patronage',
      '#attributes' => [
        'class' => ['agfirst-patronage-calculator'],
      ],
      '#attached' => [
        'library' => ['agfirst_financial_calculators/patronage_calculator'],
      ],
      '#patronage_percent' => $config->get('patronage_percent')
    ];

    return $build;
  }

}
