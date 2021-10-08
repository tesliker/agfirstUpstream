(function ($) {

  $(document).ready(function() {
    let patronagePercent = (window.agf_calc_patronage_percent || 19.42) / 100; /* 19.42% Patronage */
    /*let loanAmount = 100000;
    let interestRate = 10/100;*/
    $(document).on('click', '#btnSubmit', function(e) {
      e.preventDefault();

      if ($.isNumeric($('#tbxLoanAmount').val()) && $.isNumeric($('#tbxAnnualRate').val())) {
        $('#patronageMessage').hide();
        let loanAmount = $('#tbxLoanAmount').val().replace(/,/g, '');
        let interestRate = $('#tbxAnnualRate').val() / 100;
        let patronageDistro = calcPatronageDistro(loanAmount, interestRate, patronagePercent);
        let effectiveInterestExpense = calcEffectiveInterestExpense(loanAmount, interestRate, patronageDistro);
        let effectiveInterestRate = calcEffectiveInterestRate(interestRate, patronagePercent);
        $('span[data-id="loan-amount"]').text(parseInt(loanAmount * interestRate, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
        $('span[data-id="annual-rate"]').text($('#tbxAnnualRate').val() + '%');
        $('span[data-id="estimated-patronage"]').text('$' + patronageDistro.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
        $('span[data-id="effective-interest-expense"]').text('$' + effectiveInterestExpense.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
        $('span[data-id="effective-interest-rate"]').text((effectiveInterestRate * 100).toFixed(2) + '%');
        $('.calculator-results').addClass('reveal');
      }else{
        $('#patronageMessage').fadeIn(300);
        $('.calculator-results').removeClass('reveal');
      }
    });
    $(document).on('click', '#clearForm', function(e) {
      $('.calculator-results').removeClass('reveal');
    });
  });
  function calcPatronageDistro(loanAmount, interestRate, patronagePercent) {
    let patronageDistro = (loanAmount * interestRate) * patronagePercent;
    return (patronageDistro);
  }
  function calcEffectiveInterestExpense(loanAmount, interestRate, patronageDistro) {
    let grossInterestExpense = loanAmount * interestRate;
    let effectiveInterestExpense = grossInterestExpense - patronageDistro;
    return (effectiveInterestExpense);
  }
  function calcEffectiveInterestRate(interestRate, patronagePercent) {
    let effectiveInterestRate = interestRate * (1 - patronagePercent);
    return (effectiveInterestRate);
  }

})(jQuery);
