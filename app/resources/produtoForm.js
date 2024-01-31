function diffData(){

	let dataInicio = form_Os.detail_dataInicio.value;
	let dataTermino = form_Os.detail_dataTermino.value;
	let num = form_Os.detail_valor_hora.value;
	let valorHora = parseFloat(num.replace(',','.'));
	//let valorHora = parseFloat(num).toFixed(0);
	let valorDiff;
	let valorCalculado;

	dataInicio = moment(dataInicio, 'DD/MM/YYYY HH:mm');
	dataTermino = moment(dataTermino, 'DD/MM/YYYY HH:mm');

	valorDiff = moment.duration(dataTermino.diff(dataInicio)).asHours()

	valor = valorDiff * valorHora;
	valorCalculado = parseFloat(valor).toFixed(2);
	form_Os.detail_valor.value  = numeroParaMoeda(valorCalculado);
	Calculado = form_Os.detail_valor.value;
	console.log(valorDiff, Calculado);
}

function numeroParaMoeda(n, c, d, t)
{
    c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

$(function(){
	//$('input[name=detail_dataInicio]').blur(diffData)
	$('input[name=detail_dataTermino]').blur(diffData)
});