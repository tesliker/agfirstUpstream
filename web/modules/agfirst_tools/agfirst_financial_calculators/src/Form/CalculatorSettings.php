<?php

/**
 * @file
 * Contains Drupal\agfirst_financial_calculators\Form\CalculatorSettings.
 */

namespace Drupal\agfirst_financial_calculators\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FilemakerConfig.
 *
 * @package Drupal\agfirst_financial_calculators\Form
 */
class CalculatorSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'agfirst_financial_calculators.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'agfirst_financial_calculators_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('agfirst_financial_calculators.settings');


    /** Patronage Calculator Settings */

    $form['patronage_calculator'] = [
      '#type' => 'details',
      '#title' => 'Patronage Calculator Settings',
      '#open' => TRUE,
    ];

    $form['patronage_calculator']['patronage_percent'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Patronage Percentage'),
      '#description' => $this->t('Used in Patronage Calculator. Use numbers or decimals only (example: 4 or 4.2). If left empty, the default is "19.24" '),
      '#default_value' => empty($config->get('patronage_percent')) ? '' : $config->get('patronage_percent'),
      '#required' => FALSE,
    ];


    /** Loan Calculator Settings */

    $form['loan_calculator'] = [
      '#type' => 'details',
      '#title' => 'Loan Calculator Settings',
      '#open' => TRUE,
    ];

    $form['loan_calculator']['loan_calc_amount'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Amount of Loan'),
      '#description' => $this->t('Default loan amount.'),
      '#default_value' => $config->get('loan_calc_amount'),
      '#required' => FALSE,
    ];

    $form['loan_calculator']['loan_calc_interest'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Interest Rate'),
      '#description' => $this->t('Default interest rate of the loan (exclude percent sign).'),
      '#default_value' => $config->get('loan_calc_interest'),
      '#required' => FALSE,
    ];

    $form['loan_calculator']['loan_calc_term'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Years'),
      '#description' => $this->t('Default term of loan in years.'),
      '#default_value' => $config->get('loan_calc_term'),
      '#required' => FALSE,
    ];

    $form['loan_calculator']['loan_calc_frequency'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Payments per year'),
      '#description' => $this->t('Default number of payments per year.'),
      '#default_value' => $config->get('loan_calc_frequency'),
      '#required' => FALSE,
    ];

    /** Amortization Calculator Settings */

    $form['amortization_calculator'] = [
      '#type' => 'details',
      '#title' => 'Loan Calculator Settings',
      '#open' => TRUE,
    ];

    $form['amortization_calculator']['amortization_calc_amount'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Amount of Loan'),
      '#description' => $this->t('Default loan amount.'),
      '#default_value' => $config->get('amortization_calc_amount'),
      '#required' => FALSE,
    ];

    $form['amortization_calculator']['amortization_calc_interest'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Interest Rate'),
      '#description' => $this->t('Default interest rate of the loan (exclude percent sign).'),
      '#default_value' => $config->get('amortization_calc_interest'),
      '#required' => FALSE,
    ];

    $form['amortization_calculator']['amortization_calc_term'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Years'),
      '#description' => $this->t('Default term of loan in years.'),
      '#default_value' => $config->get('amortization_calc_term'),
      '#required' => FALSE,
    ];

    $form['amortization_calculator']['amortization_calc_frequency'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Payments per year'),
      '#description' => $this->t('Default number of payments per year.'),
      '#default_value' => $config->get('amortization_calc_frequency'),
      '#required' => FALSE,
    ];


    /** Mortgage Calculator Settings */

    $form['mortgage_calculator'] = [
      '#type' => 'details',
      '#title' => 'Mortgage Calculator Settings',
      '#open' => TRUE,
    ];

    $form['mortgage_calculator']['mort_calc_amount'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Home Price'),
      '#description' => $this->t('Default home price.'),
      '#default_value' => $config->get('mort_calc_amount'),
      '#required' => FALSE,
    ];

    $form['mortgage_calculator']['mort_calc_interest'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Annual Interest Rate'),
      '#description' => $this->t('Default interest rate of the mortgage (exclude percent sign).'),
      '#default_value' => $config->get('mort_calc_interest'),
      '#required' => FALSE,
    ];

    $form['mortgage_calculator']['mort_calc_term'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Years'),
      '#description' => $this->t('Default term of mortgage in years.'),
      '#default_value' => $config->get('mort_calc_term'),
      '#required' => FALSE,
    ];

    $form['mortgage_calculator']['mort_calc_down_payment'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Down Payment'),
      '#description' => $this->t('Default amount of downpayment.'),
      '#default_value' => $config->get('mort_calc_down_payment'),
      '#required' => FALSE,
    ];


    return parent::buildForm($form, $form_state);
  } // buildForm()

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('agfirst_financial_calculators.settings');
    $config->set('patronage_percent', $form_state->getValue('patronage_percent'));

    $config->set('loan_calc_amount', $form_state->getValue('loan_calc_amount'));
    $config->set('loan_calc_interest', $form_state->getValue('loan_calc_interest'));
    $config->set('loan_calc_term', $form_state->getValue('loan_calc_term'));
    $config->set('loan_calc_frequency', $form_state->getValue('loan_calc_frequency'));

    $config->set('mort_calc_amount', $form_state->getValue('mort_calc_amount'));
    $config->set('mort_calc_interest', $form_state->getValue('mort_calc_interest'));
    $config->set('mort_calc_term', $form_state->getValue('mort_calc_term'));
    $config->set('mort_calc_down_payment', $form_state->getValue('mort_calc_down_payment'));

    $config->save();
  }
}
