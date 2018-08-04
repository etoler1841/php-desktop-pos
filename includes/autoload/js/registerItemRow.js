function registerItemRow(data){
  let actions = `<a href='#' class='action add'>+</a>
    <a href='#' class='action subtract'>-</a>
    <a href='#' class='action remove'>&times;</a>`;
  let sign;
  if(data.type == 1){
    sign = '';
  } else {
    sign = '-';
  }
  let priceExt = Number(data.qty)*Number(data.price)*(100-Number(data.disc))/100;
  return `<tr id='reg${data.id}'>
        <td class='actions col-1'><input type='hidden' class='catID' value='${data.catID}' /><input type='hidden' class='type' value='${data.type}' />${actions}</td>
        <td class='qty col-1'>${data.qty}</td>
        <td class='desc col-5'><span class='clickEdit'>${data.name}</span></td>
        <td class='discount col-1'><span class='clickEdit'>`+round(data.disc)+`%</span></td>
        <td class='priceEa col-2'>$<span class='clickEdit'>`+round(data.price)+`</span></td>
        <td class='priceExt col-2'>${sign}$`+round(priceExt)+`</td>
       </tr>`;
}
