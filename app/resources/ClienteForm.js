function validaCpfCnpj() {

    let cpfCnpj = form_Cliente.cpf_cnpj.value;
    let reg = new RegExp("(\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2})|(\d{3}\.\d{3}\.\d{3}\-\d{2})");
    let result = reg.exec(cpfCnpj);
    console.log(result);
    form_Cliente.cpf_cnpj.value = result;
   
}

$(function(){

    $('input[name=cpf_cnpj]').off('keypress').on('keypress', validaCpfCnpj);
});