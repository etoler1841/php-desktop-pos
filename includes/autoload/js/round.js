function round(n,d = 2){
  n = parseFloat(n);
  return Number(Math.round(n+'e'+d)+'e-'+d).toFixed(d);
}
