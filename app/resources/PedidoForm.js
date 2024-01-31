function contaLinhasTabela(id){

	var tabela = document.getElementById(id);
	var linhas = tabela.getElementsByTagName('tr');
	form_Pedido.detail_sequencia.value = linhas.length;
    

    /*let itensValor[];
    itensValor.push(total_item);*/

    //console.log(linhas);
    //form_Pedido.total_pedido.value = soma;

}

function contaLinhasExcluidas(id,column){

    var tabela = document.getElementById(id);
    var linhas = tabela.getElementsByTagName('tr');

    for (var i = 1; i < linhas.length; i++) {
        
        linhas[i].cells[column].innerText=i
    }
}

function calculaTotaisPedido(){

	let preco = convertToFloatNumber(form_Pedido.detail_preco.value) || 0;
    let quantidade = convertToFloatNumber(form_Pedido.detail_quantidade.value) || 0;
    let total = convertToFloatNumber(form_Pedido.detail_total.value) || 0;
    let desconto = convertToFloatNumber(form_Pedido.detail_desconto.value) || 0;
    let total_item = convertToFloatNumber(form_Pedido.detail_total_item.value) || 0;

    total = preco * quantidade;
    total_item = total - desconto;

    form_Pedido.detail_total.value = formatMoney(total);
    form_Pedido.detail_total_item.value = formatMoney(total_item);

}

$(function(){

    $('button[name=adicionar]').off('click', contaLinhasTabela('products_list'));
    
    $('input[name=detail_total_item]').off('blur').on('blur',calculaTotaisPedido);
    $('input[name=detail_quantidade]').off('blur').on('blur',calculaTotaisPedido);
    $('input[name=detail_total]').off('blur').on('blur',calculaTotaisPedido);
    $('input[name=detail_desconto]').off('blur').on('blur',calculaTotaisPedido);
    $('input[name=detail_preco]').off('blur').on('blur',calculaTotaisPedido);

});