function contaLinhasTabela(id){

	var tabela = document.getElementById(id);
	var linhas = tabela.getElementsByTagName('tr');
	form_Nfe.product_detail_sequencia.value = linhas.length;
}

function calculaTotaisNfe(){

	let preco = convertToFloatNumber(form_Nfe.product_detail_preco.value) || 0;
    let quantidade = convertToFloatNumber(form_Nfe.product_detail_quantidade.value) || 0;
    let total = convertToFloatNumber(form_Nfe.product_detail_total.value) || 0;
    let desconto = convertToFloatNumber(form_Nfe.product_detail_desconto.value) || 0;
    let total_item = convertToFloatNumber(form_Nfe.product_detail_total_item.value) || 0;

    total = preco * quantidade;
    total_item = total - desconto;

    form_Nfe.product_detail_total.value = formatMoney(total);
    form_Nfe.product_detail_total_item.value = formatMoney(total_item);

    console.log(total_item);

}


$(function(){

    $('button[name=add_product]').off('click', contaLinhasTabela('products_list'));

    $('input[name=product_detail_total_item]').off('blur').on('blur',calculaTotaisNfe);
    $('input[name=product_detail_quantidade]').off('blur').on('blur',calculaTotaisNfe);
    $('input[name=product_detail_total]').off('blur').on('blur',calculaTotaisNfe);
    $('input[name=product_detail_desconto]').off('blur').on('blur',calculaTotaisNfe);
    $('input[name=product_detail_preco]').off('blur').on('blur',calculaTotaisNfe);

});