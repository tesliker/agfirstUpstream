function pmt(rate, nper, pv) {
  var pvif, pmt;

  pvif = Math.pow(1 + rate, nper);
  pmt = rate / (pvif - 1) * -(pv * pvif);

  return pmt;
}
