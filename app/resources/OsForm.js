function calcularTotal() {
	let quantidade = convertToFloatNumber(form_Os.detail_quantidade.value);
	let precoUnitario = convertToFloatNumber(form_Os.detail_precoUnitario.value);

	let total = parseFloat(quantidade) * parseFloat(precoUnitario);
	total = formatMoney(total);

	form_Os.detail_total.value = total;
};

function formatMoney (number, decimal, separatord, separatort) {
	var n = number,
		c = isNaN(decimal = Math.abs(decimal)) ? 2 : decimal,
		d = separatord == undefined ? "," : separatord,
		t = separatort == undefined ? "." : separatort,
		s = n < 0 ? "-" : "",
		i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
		j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

function convertToFloatNumber(value) {
	value = value.toString();
	if (value.indexOf('.') !== -1 || value.indexOf(',') !== -1) {
		if (value.indexOf('.') >  value.indexOf(',')) {
			return parseFloat(value.replace(/,/gi,''));
		} else {
			return parseFloat(value.replace(/\./gi,'').replace(/,/gi,'.'));
		}
	} else {
		return isNaN(parseFloat(value)) ? 0 : parseFloat(value);
	}
};

function checkSaldoProduto() {
	let quantidade = convertToFloatNumber(form_Os.detail_quantidade.value);
	if (quantidade <= 0 ) {
		return;
	}

	let detail_estoque = form_Os.detail_estoque.value;

	console.log(detail_estoque);

	if (detail_estoque === '') {
		return;
	}


	__adianti_post_lookup('form_Os', 'class=OsForm&method=onCheckSaldoProduto', form_Os.detail_dataInicio.id, calcularTotal)
}

function checkHorarioAltorizadoInicio() {
	__adianti_post_lookup('form_Os', 'class=OsForm&method=onCheckHorario', form_Os.detail_dataInicio.id, 'callback')
}

function checkHorarioAltorizadoTermino() {
	__adianti_post_lookup('form_Os', 'class=OsForm&method=onCheckHorario', form_Os.detail_dataTermino.id, 'callback')
}

function checkPrazoAtendimento() {
	__adianti_post_lookup('form_Os', 'class=OsForm&method=onCheckPrazoAtendimento', form_Os.detail_dataTermino.id, 'callback')
}

function diffData(){

	let dataInicio = form_Os.detail_dataInicio.value;
	if (!dataInicio) {
		return false;
	}
	let dataTermino = form_Os.detail_dataTermino.value;
	if (!dataTermino) {
		return false;
	}
	let num = form_Os.detail_valor_hora.value;
	let valorHora = parseFloat(num.replace('.','').replace(',','.'));
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
	// console.log(valorDiff, Calculado);
}

function numeroParaMoeda(n, c, d, t)
{
    c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

$(function(){
	//$('input[name=detail_dataInicio]').blur(diffData)
	$('input[name=detail_dataTermino]').change(function () {
		diffData();
		// checkHorarioAltorizadoInicio();
		checkHorarioAltorizadoTermino();
		checkPrazoAtendimento();
	});

	$('input[name=detail_dataInicio]').change(function () {
		diffData();
		checkHorarioAltorizadoInicio();
	});
	$('input[name=detail_quantidade]').blur(function () {
		checkSaldoProduto();
	});

	$('select[name=detail_estoque]').change(function () {
		checkSaldoProduto();
	});

	$('input[name=detail_precoUnitario]').blur(calcularTotal);
});
