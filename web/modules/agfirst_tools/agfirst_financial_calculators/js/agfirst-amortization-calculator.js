(function($, Drupal, drupalSettings) {
  Drupal.behaviors.agfirstAmortizationCalculator = {
    attach: function (context) {

      $('#buttonCalculate').once().on('click', function(event) {

        let thisCalc = $(this).closest('.amortization-calculator');

        let principal = parseFloat(thisCalc.find('#loanAmount').val().replace(/,/g, ''));

        let interestRate = parseFloat(thisCalc.find('#interestRate').val().replace(/,/g, ''));
        let months = parseInt(thisCalc.find('#months').val());
        let interest = interestRate / 100.0 / 12;

        // Validate inputs:
        let thisErrorMessage = thisCalc.find('.form-error-message');
        thisErrorMessage.text('');
        if (!$.isNumeric(principal) || !$.isNumeric(interestRate) || !$.isNumeric(months)) {
          thisErrorMessage.text('Please check your values and try again.');
          return false;
        }

        let payment = principal * (interest + interest / (Math.pow(1 + interest, months) -1 ));
        let data = '';

        let installment;
        let balance = principal;
        let totalInterest = 0;
        for (installment = 1; installment <= months; installment++) {

          let toInterest = balance * interest;

          totalInterest = totalInterest + toInterest;

          let toPrincipal = payment - toInterest;
          balance = balance - toPrincipal;
          if (balance <= 0) {

            balance = 0;
          }
          data += buildRow(installment, payment, toPrincipal, toInterest, totalInterest, balance);

        }


        // Show the results:

        thisCalc.find('.amortization-schedule tbody').html(data);
        thisCalc.find('.monthlyPayment').text(toCurrency(payment));

        thisCalc.find('.totalPrincipal').text(toCurrency(principal));
        thisCalc.find('.totalInterest').text(toCurrency(totalInterest));
        if ($.isNumeric(payment) && $.isNumeric(principal) && $.isNumeric(totalInterest)) {

          thisCalc.find('.payment-summary').fadeIn(100);
          thisCalc.find('.payment-schedule').fadeIn(100);
        } else {
          thisCalc.find('.payment-summary').hide();
          thisCalc.find('.payment-schedule').hide();
        }

      }).fancybox({});

      let buildRow = function(installment, payment, toPrincipal, toInterest, totalInterest, balance) {

        let row = '<tr>';
        row += '<td>' + installment + '</td>';
        row += '<td>' + toCurrency(payment) + '</td>';
        row += '<td>' + toCurrency(toPrincipal) + '</td>';
        row += '<td>' + toCurrency(toInterest) + '</td>';
        row += '<td>' + toCurrency(totalInterest) + '</td>';
        row += '<td>' + toCurrency(balance) + '</td>';
        row += '</tr>';

        return row;

      };

      let toCurrency = function(value) {
        return '$' + value.toFixed(2).replace(/(\d)(?=(\d{3})+\.\d\d$)/g,"$1,");
      };
    }
  };

})(jQuery, Drupal, drupalSettings);
