<?php

namespace Drupal\agfirst_financial_calculators\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an 'Amortiation Calculator' block.
 *
 * @Block(
 *  id = "amortization_calculator",
 *  admin_label = @Translation("Amortization Calculator"),
 * )
 */
class AmortizationCalculator extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $config = \Drupal::config('agfirst_financial_calculators.settings');

    $build['amortization_calculator'] = [
      '#theme' => 'agfirst_financial_calculators_amortization',
      '#attributes' => [
        'class' => ['agfirst-amortization-calculator'],
      ],
      '#attached' => [
        'library' => ['agfirst_financial_calculators/amortization_calculator'],
      ],
      '#amortization_calc_amount' => $config->get('amortization_calc_amount'),
      '#amortization_calc_interest' => $config->get('amortization_calc_interest'),
      '#amortization_calc_term' => $config->get('amortization_calc_term'),
      '#amortization_calc_frequency' => $config->get('amortization_calc_frequency'),
    ];

    return $build;
  }

}
