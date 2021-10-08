(function ($) {

  function getDataSet() {
    let output = {
      homePrice: parseFloat($('#mortgage-homePrice').val().replace(/,/g, '')),
      downPayment: parseFloat($('#mortgage-downPayment').val().replace(/,/g, '')),
      interestRate: parseFloat($('#mortgage-interestRate').val().replace(/,/g, '')),
      years: parseInt($('#mortgage-years').val().replace(/,/g, '')),
    };

    output['paymentsPerYear'] = 12;
    output['loanAmount'] = output.homePrice - output.downPayment;
    output['numberOfPayments'] = output.paymentsPerYear * output.years;
    output['payment'] = pmt(output.interestRate / 100 / output.paymentsPerYear, output.numberOfPayments, -output.loanAmount);

    $("#mortgage-loanAmount").keydown(function (e) {
      if (e.keyCode === 188) {
        e.preventDefault();
      }
    });

    return output;

  }

  function reload() {
    let ds = getDataSet();

    // Validate input / output as numeric.
    if ($.isNumeric($('#mortgage-homePrice').val().replace(/,/g, '')) &&
      $.isNumeric($('#mortgage-downPayment').val().replace(/,/g, '')) &&
      $.isNumeric($('#mortgage-interestRate').val().replace(/,/g, '')) &&
      $.isNumeric($('#mortgage-years').val().replace(/,/g, '')) &&
      $.isNumeric(ds.payment.toFixed(2))) {

      $('#mortgage-paymentAmount').text('$' + ds.payment.toFixed(2));
      $('#mortgageCalcMessage').hide();
      $('#mortgageCalcResult').fadeIn(300);
    } else {
      $('#mortgage-paymentAmount').text('');
      $('#mortgageCalcMessage').fadeIn(300);
      $('#mortgageCalcResult').hide();
    }
  }

  $(document).on('keyup', '.user-input', reload);

  $(document).ready(function () {
    reload();
  });

})(jQuery);
