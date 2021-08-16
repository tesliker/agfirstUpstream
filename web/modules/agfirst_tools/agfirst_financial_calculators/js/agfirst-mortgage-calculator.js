(function ($) {

  function getDataSet() {
    var output = {};
    var homePrice = output.loanAmount = parseFloat($('#mortgage-homePrice').val());
    var downPayment = output.loanAmount = parseFloat($('#mortgage-downPayment').val());
    var loanAmount = homePrice - downPayment;
    var interestRate = output.interestRate = parseFloat($('#mortgage-interestRate').val());
    var paymentsPerYear = 12;
    var years = output.years = parseInt($('#mortgage-years').val());
    var numberOfPayments = output.numberOfPayments = paymentsPerYear * years;

    var payment = output.payment = pmt(interestRate / 100 / paymentsPerYear, numberOfPayments, -loanAmount);
    $("#mortgage-loanAmount").keydown(function (e) {
      if (e.keyCode === 188) {
        e.preventDefault();
      }
    });

    output.schedule = computeSchedule(loanAmount,
      interestRate,
      paymentsPerYear,
      years,
      payment);
    return output;

  }

  function reload() {
    var ds = getDataSet();

    if ($.isNumeric(ds.payment.toFixed(2))) {
      $('#mortgage-paymentAmount').text('$' + ds.payment.toFixed(2));
      $('#mortgageCalcResult').show();
    } else {
      $('#mortgage-paymentAmount').text('');
      $('#mortgageCalcResult').hide();
    }
  }


  $(document).on('keyup', '.user-input', reload);

  $(document).ready(function () {
    reload();
  });

})(jQuery);
