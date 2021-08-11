<?php

namespace Drupal\agfirst_financial_calculators\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'MortgageCalculator' block.
 *
 * @Block(
 *  id = "mortgage_calculator",
 *  admin_label = @Translation("Mortgage Calculator"),
 * )
 */
class MortgageCalculator extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $config = \Drupal::config('agfirst_financial_calculators.settings');

    $build['mortgage_calculator'] = [
      '#theme' => 'agfirst_financial_calculators_mortgage',
      '#attributes' => [
        'class' => ['agfirst-mortgage-calculator'],
      ],
      '#attached' => [
        'library' => ['agfirst_financial_calculators/mortgage_calculator'],
      ],
      '#mort_calc_amount' => $config->get('mort_calc_amount'),
      '#mort_calc_interest' => $config->get('mort_calc_interest'),
      '#mort_calc_term' => $config->get('mort_calc_term'),
      '#mort_calc_down_payment' => $config->get('mort_calc_down_payment'),
    ];

    return $build;
  }

}
