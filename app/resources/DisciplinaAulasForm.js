function calcularTotais(){

    let a_1bim = form_DisciplinaAulasForm.detail_a_1bim.value || 0;
    let a_2bim = form_DisciplinaAulasForm.detail_a_2bim.value || 0;
    let a_3bim = form_DisciplinaAulasForm.detail_a_3bim.value || 0;
    let a_4bim = form_DisciplinaAulasForm.detail_a_4bim.value || 0;
    let ta_anual = form_DisciplinaAulasForm.detail_ta_anual.value || 0;
    let soma = 0;

    soma = parseInt(a_1bim) + parseInt(a_2bim) + parseInt(a_3bim) + parseInt(a_4bim);

    form_DisciplinaAulasForm.detail_ta_anual.value = soma;
}


$(function(){
    // evento de saida do valor unitario
    $('input[name=detail_a_1bim]').off('blur').on('blur', calcularTotais);
    $('input[name=detail_a_2bim]').off('blur').on('blur', calcularTotais);
    $('input[name=detail_a_3bim]').off('blur').on('blur', calcularTotais);
    $('input[name=detail_a_4bim]').off('blur').on('blur', calcularTotais);

});