function calcularTotaisAulas(){

    let a_1bim = form_ApontamentoForm.a_1bim.value || 0;
    let a_2bim = form_ApontamentoForm.a_2bim.value || 0;
    let a_3bim = form_ApontamentoForm.a_3bim.value || 0;
    let a_4bim = form_ApontamentoForm.a_4bim.value || 0;
    
    let soma = 0;

    soma = parseInt(a_1bim) + parseInt(a_2bim) + parseInt(a_3bim) + parseInt(a_4bim);

    form_ApontamentoForm.ta_anual.value = soma;
}

function calcularTotaisPresencasFaltas(){

    let a_1bim = form_ApontamentoForm.a_1bim.value || 0;
    let a_2bim = form_ApontamentoForm.a_2bim.value || 0;
    let a_3bim = form_ApontamentoForm.a_3bim.value || 0;
    let a_4bim = form_ApontamentoForm.a_4bim.value || 0;

    let p_1bim = form_ApontamentoForm.p_1bim.value || 0;
    let p_2bim = form_ApontamentoForm.p_2bim.value || 0;
    let p_3bim = form_ApontamentoForm.p_3bim.value || 0;
    let p_4bim = form_ApontamentoForm.p_4bim.value || 0;

    somaPresencasFaltas = parseInt(p_1bim) + parseInt(p_2bim) + parseInt(p_3bim) + parseInt(p_4bim);

    form_ApontamentoForm.tp_anual.value = somaPresencasFaltas;

    if(parseInt(p_1bim) != 0){
        form_ApontamentoForm.f_1bim.value = parseInt(a_1bim) - parseInt(p_1bim);
    }else{
        form_ApontamentoForm.f_1bim.value = 0;
    }

    if(parseInt(p_2bim) != 0){
        form_ApontamentoForm.f_2bim.value = parseInt(a_2bim) - parseInt(p_2bim);
    }else{
        form_ApontamentoForm.f_2bim.value = 0;
    }
    
    if(parseInt(p_3bim) != 0){
        form_ApontamentoForm.f_3bim.value = parseInt(a_3bim) - parseInt(p_3bim);
    }else{
        form_ApontamentoForm.f_3bim.value = 0;
    }

    if(parseInt(p_4bim) != 0){
        form_ApontamentoForm.f_4bim.value = parseInt(a_4bim) - parseInt(p_4bim);
    }else{
        form_ApontamentoForm.f_4bim.value = 0;
    }

    somaPresencasFaltasAno = parseInt(form_ApontamentoForm.f_1bim.value) + parseInt(form_ApontamentoForm.f_2bim.value) + parseInt(form_ApontamentoForm.f_3bim.value) + parseInt(form_ApontamentoForm.f_4bim.value);
    form_ApontamentoForm.tf_anual.value = somaPresencasFaltasAno;
    
    
    //calculo das frequencias em %
    p1bim = parseInt(p_1bim ) * (100 / parseInt(a_1bim));
    form_ApontamentoForm.ft_1bim.value  = Math.round(p1bim);

    p2bim = parseInt(p_2bim ) * (100 / parseInt(a_2bim));
    form_ApontamentoForm.ft_2bim.value  = Math.round(p2bim);

    p3bim = parseInt(p_3bim ) * (100 / parseInt(a_3bim));
    form_ApontamentoForm.ft_3bim.value  = Math.round(p3bim);

    p4bim = parseInt(p_4bim ) * (100 / parseInt(a_4bim));
    form_ApontamentoForm.ft_4bim.value  = Math.round(p4bim);

    FT = parseInt(p1bim) + parseInt(p2bim) + parseInt(p3bim) + parseInt(p4bim);
    FTarredondado = FT / 4;
    form_ApontamentoForm.ft_anual.value =  Math.round(FTarredondado);
}

function calculaNotas(){

    let n_1bim = form_ApontamentoForm.n_1bim.value || 0;
    let n_2bim = form_ApontamentoForm.n_2bim.value || 0;
    let REC12 = form_ApontamentoForm.REC12.value || 0;
    let MDS1 = form_ApontamentoForm.n_3bim.value || 0;
    let MDS2 = form_ApontamentoForm.MDS2.value || 0;
    let MFA = form_ApontamentoForm.MFA.value || 0;
    
    MS1 = ( parseFloat(n_1bim)  +  parseFloat(n_2bim ) ) / 2;

    if( parseFloat(n_2bim ) > 0){
        form_ApontamentoForm.MS1.value = formatNota(MS1);
    }
    
    MDS1 = (parseFloat(REC12) + parseFloat(MS1)) / 2;

    if(parseFloat(n_2bim ) > 0){
        if(parseFloat(REC12) < parseFloat(MS1) ){
            form_ApontamentoForm.MDS1.value = formatNota(MS1);
        }else{
            form_ApontamentoForm.MDS1.value = formatNota(MDS1);
        }
    }

    let n_3bim = form_ApontamentoForm.n_3bim.value || 0;
    let n_4bim = form_ApontamentoForm.n_4bim.value || 0;
    let REC34 = form_ApontamentoForm.REC34.value || 0;
    let PF = form_ApontamentoForm.PF.value || null;
    
    MS2 = ( parseFloat(n_3bim)  +  parseFloat(n_4bim ) ) / 2;

    if( parseFloat(n_4bim ) > 0){
        form_ApontamentoForm.MS2.value = formatNota(MS2);
    }
    
    MDS2 = ( parseFloat(REC34) + parseFloat(MS2) ) / 2;

    if(parseFloat(n_4bim ) > 0){
        if(parseFloat(REC34) < parseFloat(MS2) ){
            form_ApontamentoForm.MDS2.value = formatNota(MS2);
        }else{
            form_ApontamentoForm.MDS2.value = formatNota(MDS2);
        }
    }

    
    media = 7;
    if(parseFloat(n_4bim) > 0){

        let PF = form_ApontamentoForm.PF.value || 0;
        MDS2 = form_ApontamentoForm.MDS2.value;
        MA = ( parseFloat(MDS1) + parseFloat(MDS2) ) / 2;
        form_ApontamentoForm.MA.value = formatNota(MA);

        if( parseFloat(MA) >= media || parseFloat(PF) != 0){
 
            if(parseFloat(PF) > parseFloat(MFA)){
                totalFinalAnual = ( ( parseFloat(MA) * 2 ) + parseFloat(PF) ) * (1 / 3);
                form_ApontamentoForm.MFA.value = formatNota(totalFinalAnual);
            }else{
                form_ApontamentoForm.MFA.value = formatNota(MA);
            }
            
        }   
    }

    MFA = form_ApontamentoForm.MFA.value;
    if(parseFloat(MFA) > 0){
        if(parseFloat(MFA) >= media){
            form_ApontamentoForm.resultado.value = 'AP';
        }else{
            form_ApontamentoForm.resultado.value = 'RP';
        }
    }
    
}

$(function(){
    // evento de saida do valor unitario
    $('input[name=a_1bim]').off('blur').on('blur', calcularTotaisAulas);
    $('input[name=a_2bim]').off('blur').on('blur', calcularTotaisAulas);
    $('input[name=a_3bim]').off('blur').on('blur', calcularTotaisAulas);
    $('input[name=a_4bim]').off('blur').on('blur', calcularTotaisAulas);

    $('input[name=p_1bim]').off('blur').on('blur', calcularTotaisPresencasFaltas);
    $('input[name=p_2bim]').off('blur').on('blur', calcularTotaisPresencasFaltas);
    $('input[name=p_3bim]').off('blur').on('blur', calcularTotaisPresencasFaltas);
    $('input[name=p_4bim]').off('blur').on('blur', calcularTotaisPresencasFaltas);

    $('input[name=n_1bim]').off('blur').on('blur', calculaNotas);
    $('input[name=n_2bim]').off('blur').on('blur', calculaNotas);
    $('input[name=n_3bim]').off('blur').on('blur', calculaNotas);
    $('input[name=n_4bim]').off('blur').on('blur', calculaNotas);
    $('input[name=REC12]').off('blur').on('blur', calculaNotas);
    $('input[name=REC34]').off('blur').on('blur', calculaNotas);
    $('input[name=PF]').off('blur').on('blur', calculaNotas);

});