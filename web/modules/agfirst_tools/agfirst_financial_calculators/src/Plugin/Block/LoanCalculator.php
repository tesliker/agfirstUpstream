<?php

namespace Drupal\agfirst_financial_calculators\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'LoanCalculator' block.
 *
 * @Block(
 *  id = "loan_calculator",
 *  admin_label = @Translation("Loan Payment Calculator"),
 * )
 */
class LoanCalculator extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $config = \Drupal::config('agfirst_financial_calculators.settings');

    $build['loan_calculator'] = [
      '#theme' => 'agfirst_financial_calculators_loan',
      '#attributes' => [
        'class' => ['agfirst-loan-calculator'],
      ],
      '#attached' => [
        'library' => ['agfirst_financial_calculators/loan_calculator'],
      ],
      '#loan_calc_amount' => $config->get('loan_calc_amount'),
      '#loan_calc_interest' => $config->get('loan_calc_interest'),
      '#loan_calc_term' => $config->get('loan_calc_term'),
      '#loan_calc_frequency' => $config->get('loan_calc_frequency'),
    ];

    return $build;
  }

}
