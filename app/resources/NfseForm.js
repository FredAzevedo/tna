
function calcularTotal() {
    // pega o valor unitario e converte para numero, assumindo 0 (zero) caso não consiga converter.
    let unitario = convertToFloatNumber(form_NFSe.detail_valor.value) || 0;

    // pega a quantidade e converte para numero, assumindo 0 (zero) caso não consiga converter.
    let quantidade = convertToFloatNumber(form_NFSe.detail_quantidade.value) || 0;

    let total = unitario * quantidade;

    form_NFSe.detail_total_item.value = formatMoney(total);
    console.log(unitario, quantidade);
}

function calcularDeducao() {

    // pega o valor da dedição e converte para numero, assumindo 0 (zero) caso não consiga converter.
    let deducao = convertToFloatNumber(form_NFSe.deducoes.value) || 0;

    // pega a base do calculo e converte para numero, assumindo 0 (zero) caso não consiga converter.
    let base_calculo = convertToFloatNumber(form_NFSe.base_calculo.value) || 0;

    // calcula o total do serviço
    let total_servico = base_calculo - deducao;

    if (base_calculo > 0 && total_servico > 0) {
        form_NFSe.total_servico.value = formatMoney(total_servico);
    } else {
        form_NFSe.total_servico.value = formatMoney(0.0);
    }

    console.log('calcularDeducao', total_servico, formatMoney(total_servico));
}

function calcularRetencao(){

    let totalServico = convertToFloatNumber(form_NFSe.total_servico.value) || 0;
    let RetCofins = convertToFloatNumber(form_NFSe.RetCofins.value) || 0;
    let RetCsll = convertToFloatNumber(form_NFSe.RetCsll.value) || 0;
    let RetInss = convertToFloatNumber(form_NFSe.RetInss.value) || 0;
    let RetIrrf = convertToFloatNumber(form_NFSe.RetIrrf.value) || 0;
    let RetPis = convertToFloatNumber(form_NFSe.RetPis.value) || 0;

    RetCofins = totalServico * RetCofins / 100;
    form_NFSe.vRetCofins.value = formatMoney(RetCofins);
    
    RetCsll = totalServico * RetCsll / 100;
    form_NFSe.vRetCsll.value = formatMoney(RetCsll);

    RetInss = totalServico * RetInss / 100;
    form_NFSe.vRetInss.value = formatMoney(RetInss);

    RetIrrf = totalServico * RetIrrf / 100;
    form_NFSe.vRetIrrf.value = formatMoney(RetIrrf);

    RetPis = totalServico * RetPis / 100;
    form_NFSe.vRetPis.value = formatMoney(RetPis);

}

function changeISSRetido(p) {
    
    let reter_iss = form_NFSe.ISSretido.value;

    if (reter_iss == '0') {
        tfield_disable_field('form_NFSe', 'ISSaliquota');
        form_NFSe.ISSaliquota.value = formatMoney(0.00);
        form_NFSe.ISSvalor.value = formatMoney(0.00);
    } else {
        tfield_enable_field('form_NFSe', 'ISSaliquota');
    }
}

function calcISSvalor(){

    let aliquota = convertToFloatNumber(form_NFSe.ISSaliquota.value) || 0;
    let totalServico = convertToFloatNumber(form_NFSe.total_servico.value) || 0;
    let valor = aliquota * totalServico / 100;
    form_NFSe.ISSvalor.value = formatMoney(valor);
}


$(function(){
    // evento de saida do valor unitario
    $('input[name=detail_valor]').off('blur').on('blur', calcularTotal);
    $('input[name=detail_quantidade]').off('blur').on('blur',calcularTotal);

    $('input[name=deducoes]').off('blur').on('blur',calcularDeducao);

    $('input[name=RetCofins]').off('blur').on('blur',calcularRetencao);
    $('input[name=RetCsll]').off('blur').on('blur',calcularRetencao);
    $('input[name=RetInss]').off('blur').on('blur',calcularRetencao);
    $('input[name=RetIrrf]').off('blur').on('blur',calcularRetencao);
    $('input[name=RetPis]').off('blur').on('blur',calcularRetencao);
    $("input[name=ISSaliquota]").off('blur').on('blur', calcISSvalor);
    //$("select[name=ISSretido]").off('change').on('change', changeISSRetido);
});