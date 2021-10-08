(function ($) {

  function getDataSet() {
    let output = {
      loanAmount: parseFloat($('#loanAmount').val().replace(/,/g, '')),
      interestRate: parseFloat($('#interestRate').val().replace(/,/g, '')),
      years: parseInt($('#years').val().replace(/,/g, '')),
      paymentsPerYear: parseInt($('#paymentsPerYear').val().replace(/,/g, '')),
    };

    output['numberOfPayments'] = output.paymentsPerYear * output.years;
    output['payment'] = pmt(output.interestRate / 100 / output.paymentsPerYear, output.numberOfPayments, -output.loanAmount);

    $("#loanAmount").keydown(function (e) {
      if (e.keyCode === 188) {
        e.preventDefault();
      }
    });

    return output;
  }

  function reload() {
    let ds = getDataSet();

    // Validate input / output as numeric.
    if ($.isNumeric($('#loanAmount').val().replace(/,/g, '')) &&
      $.isNumeric($('#interestRate').val().replace(/,/g, '')) &&
      $.isNumeric($('#years').val().replace(/,/g, '')) &&
      $.isNumeric($('#paymentsPerYear').val().replace(/,/g, '')) &&
      $.isNumeric(ds.payment.toFixed(2))) {

      $('#paymentAmount').text('$' + ds.payment.toFixed(2));
      $('#loanCalcMessage').hide();
      $('#loanCalcResult').fadeIn(300);
    } else {
      $('#paymentAmount').text('');
      $('#loanCalcMessage').fadeIn(300);
      $('#loanCalcResult').hide();
    }
  }

  $(document).on('keyup', '.user-input', reload);

  $(document).ready(function () {
    reload();
  });

})(jQuery);
