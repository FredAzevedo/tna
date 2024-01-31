loading = true;

//Adianti.registerState = false;

Application = {};
Application.translation = {
    'en' : {
        'loading' : 'Loading',
        'close'   : 'Close',
        'insert'  : 'Insert',
        'open_new_tab' : 'Open on a new tab'
    },
    'pt' : {
        'loading' : 'Carregando',
        'close'   : 'Fechar',
        'insert'  : 'Inserir',
        'open_new_tab' : 'Abrir em uma nova aba'
    },
    'es' : {
        'loading' : 'Cargando',
        'close'   : 'Cerrar',
        'insert'  : 'Insertar',
        'open_new_tab' : 'Abrir en una nueva pestaña'
    }
};

Adianti.onClearDOM = function(){
	/* $(".select2-hidden-accessible").remove(); */
	/* $(".colorpicker-hidden").remove(); */
	$(".pcr-app").remove();
	$(".select2-display-none").remove();
	$(".tooltip.fade").remove();
	$(".select2-drop-mask").remove();
	/* $(".autocomplete-suggestions").remove(); */
	$(".datetimepicker").remove();
	$(".note-popover").remove();
	$(".dtp").remove();
	$("#window-resizer-tooltip").remove();
};


function showLoading() 
{ 
    if(loading)
    {
        __adianti_block_ui(Application.translation[Adianti.language]['loading']);
    }
}

Adianti.onBeforeLoad = function(url) 
{ 
    loading = true; 
    setTimeout(function(){showLoading()}, 400);
    if (url.indexOf('&static=1') == -1 && url.indexOf('&noscroll=1') == -1) {
        $("html, body").animate({ scrollTop: 0 }, "fast");
    }
};

Adianti.onAfterLoad = function(url, data)
{ 
    loading = false; 
    __adianti_unblock_ui( true );
    
    // Fill page tab title with breadcrumb
    // window.document.title  = $('#div_breadcrumbs').text();
};

// set select2 language
$.fn.select2.defaults.set('language', $.fn.select2.amd.require("select2/i18n/pt"));


function __adianti_input_fuse_search(input_search, attribute, selector)
{
    var stack_search = new Array();
    $(selector).each(function() {
        stack_search.push({
            id: $(this).attr('id'),
            name: $(this).attr(attribute)
        });

    });
    
    var fuse = new Fuse(stack_search, {
            keys: ['name'],
            id: 'id',
            threshold: 0.2
        });
        
    $(input_search).on('keyup', function(){
        var result = fuse.search($(this).val());

        $(selector + '['+attribute+']').hide();
        if(result.length > 0) {
            for (var i = 0; i < result.length; i++) {
                var query = '#'+result[i];
                $(query).show();
            }
        }
        else {
            $(selector + '['+attribute+']').show();
        }
    });
}

function __adianti_builder_edit_page()
{
    var url = Adianti.currentURL;
    url = url.replace('engine.php?', '');
    var params = __adianti_query_to_json(url);
    var controller = params.class;
    __adianti_load_page('index.php?class=SystemPageService&method=editPage&static=1&controller='+controller);
}

function __adianti_builder_get_codes()
{
    __adianti_load_page('index.php?class=SystemPageBatchUpdate');
}

function __adianti_builder_update_menu()
{
    __adianti_load_page('index.php?class=SystemMenuUpdate&method=onAskUpdate&register_state=false');
}

function __adianti_builder_update_permissions()
{
    __adianti_load_page('index.php?class=SystemPermissionUpdate&method=onAskUpdate&register_state=false');
}


/*
* Sobrescrevendo função de inicialização do calendar
* */

function ffullcalendar_start(id, editable, defaultView, currentDate, language, events, resources, day_click_action, event_click_action, event_update_action, min_time, max_time, hidden_days, movable, resizable, customAction, customText, customConfig)
{
    var drag_status   = 0;
    var resize_status = 0;

    var onChangeViewAction = customConfig.onChangeView || null;

    var defaultFunctionRender = function () {
        setTimeout( function() {__adianti_process_popover()},100);
    };

    var renderFunction;

    if (onChangeViewAction !== null) {
        renderFunction = function (view, element) {
            // console.log(view);
            __adianti_ajax_exec(onChangeViewAction+"&type="+view.type+"&current_date="+ view.calendar.currentDate.format("YYYY-MM-DD"));
            defaultFunctionRender();
        };
    } else {
        renderFunction = function (view,element) {
            defaultFunctionRender();
        }
    }

    console.log(customAction, customText);

    let customButton = undefined;
    let left = 'prev,next today';
    if (customText) {
        customButton = {
            custom1: {
                text: customText,
                click: function () {
                    __adianti_load_page('index.php?class='+customAction);
                }
            }
        };

        left += ' custom1';
    }
    // customButtons: {
    //     custom1: {
    //         text: 'Fred',
    //             click: function() {
    //             alert('clicked custom button 1!');
    //         }
    //     }
    // },
    $('#'+id).fullCalendar({
        // define a licença do scheduler.  CC-Attribution-NonCommercial-NoDerivatives / GPL-My-Project-Is-Open-Source
        schedulerLicenseKey: customConfig.schedulerLicenseKey || 'CC-Attribution-NonCommercial-NoDerivatives',
        header: {
            left: left,
            center: 'title',
            //alteração dos botões da direita. Anterioremnte era 'month,agendaWeek,agendaDay'
            right: 'month,agendaWeek,agendaDay' //'month,agendaWeek,agendaDay' resourceTimeGridDay | timelineDay
        },
        aspectRatio: customConfig.aspectRatio || 2.00,
        height: 'auto',  // define a altura.
        scrollTime: customConfig.scrollTime || '00:00',
        resourceAreaWidth: customConfig.resourceAreaWidth || 220,
        resourceLabelText: customConfig.resourceLabelText || 'Resources',
        customButtons: customButton,
        hiddenDays: hidden_days,
        defaultDate: currentDate,
        defaultView: defaultView,
        allDaySlot: false,
        viewRender: renderFunction,
        minTime: min_time,
        maxTime: max_time,
        slotLabelFormat : 'HH:mm',
        lang: language,
        editable: editable,
        eventLimit: true, // allow "more" link when too many events
        events: events,
        resources: resources,
        eventDurationEditable: resizable,
        eventStartEditable: movable,
        eventRender: function (event, element) {
            element.find('.fc-title').html(event.title);
        },
        dayClick: function(date, jsEvent, view) {
            if (day_click_action !== '' && drag_status == 0 && resize_status == 0 ) {
                __adianti_load_page(day_click_action+"&date="+date.format()+"&view="+view.name);
            }
        },
        eventClick: function(calEvent, jsEvent, view) {
            if (event_click_action !== '' && drag_status == 0 && resize_status == 0 ) {
                __adianti_load_page(event_click_action+"&id="+calEvent.id+"&key="+calEvent.id+"&view="+view.name);
            }
        },
        eventDragStart : function(calEvent, jsEvent, ui, view) {
            drag_status = 1;
        },
        eventDragStop : function(calEvent, jsEvent, ui, view) {
            drag_status = 0;
        },
        eventDrop : function(calEvent, jsEvent, ui, view) {
            if (event_update_action !== '') {
                __adianti_ajax_exec(event_update_action+"&id="+calEvent.id+"&key="+calEvent.id+"&start_time="+calEvent.start.format()+"&end_time="+calEvent.end.format());
            }
        },
        eventResizeStart : function(calEvent, jsEvent, ui, view) {
            resize_status = 1;
        },
        eventResizeStop : function(calEvent, jsEvent, ui, view) {
            resize_status = 0;
        },
        eventResize : function(calEvent, jsEvent, ui, view) {
            if (event_update_action !== '') {
                __adianti_ajax_exec(event_update_action+"&id="+calEvent.id+"&key="+calEvent.id+"&start_time="+calEvent.start.format()+"&end_time="+calEvent.end.format());
            }
        },
        eventAfterAllRender: function() {
            __adianti_process_popover();
        }
    });
}

function Osfullcalendar_start(id, editable, defaultView, currentDate, language, events, day_click_action, event_click_action, event_update_action, min_time, max_time, hidden_days, movable, resizable, customAction, customText)
{
    var drag_status   = 0;
    var resize_status = 0;

    console.log(customAction, customText);

    let customButton = undefined;
    let left = 'prev,next today';
    if (customText) {
        customButton = {
            custom1: {
                text: customText,
                click: function () {
                    __adianti_load_page('index.php?class='+customAction);
                }
            }
        };

        left += ' custom1';
    }
    // customButtons: {
    //     custom1: {
    //         text: 'Fred',
    //             click: function() {
    //             alert('clicked custom button 1!');
    //         }
    //     }
    // },
    $('#'+id).fullCalendar({
        header: {
            left: left,
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        height: 1100,
        contentHeight: 1100,
        customButtons: customButton,
        hiddenDays: hidden_days,
        defaultDate: currentDate,
        defaultView: defaultView,
        allDaySlot: false,
        viewRender: function(view,element){setTimeout( function() {__adianti_process_popover()},100)},
        minTime: min_time,
        maxTime: max_time,
        slotLabelFormat : 'HH:mm',
        lang: language,
        editable: editable,
        eventLimit: true, // allow "more" link when too many events
        events: events,
        eventDurationEditable: resizable,
        eventStartEditable: movable,
        eventRender: function (event, element) {
            element.find('.fc-title').html(event.title);
        },
        dayClick: function(date, jsEvent, view) {
            if (day_click_action !== '' && drag_status == 0 && resize_status == 0 ) {
                __adianti_load_page(day_click_action+"&date="+date.format()+"&view="+view.name);
            }
        },
        eventClick: function(calEvent, jsEvent, view) {
            if (event_click_action !== '' && drag_status == 0 && resize_status == 0 ) {
                __adianti_load_page(event_click_action+"&id="+calEvent.id+"&key="+calEvent.id+"&view="+view.name);
            }
        },
        eventDragStart : function(calEvent, jsEvent, ui, view) {
            drag_status = 1;
        },
        eventDragStop : function(calEvent, jsEvent, ui, view) {
            drag_status = 0;
        },
        eventDrop : function(calEvent, jsEvent, ui, view) {
            if (event_update_action !== '') {
                __adianti_ajax_exec(event_update_action+"&id="+calEvent.id+"&key="+calEvent.id+"&start_time="+calEvent.start.format()+"&end_time="+calEvent.end.format());
            }
        },
        eventResizeStart : function(calEvent, jsEvent, ui, view) {
            resize_status = 1;
        },
        eventResizeStop : function(calEvent, jsEvent, ui, view) {
            resize_status = 0;
        },
        eventResize : function(calEvent, jsEvent, ui, view) {
            if (event_update_action !== '') {
                __adianti_ajax_exec(event_update_action+"&id="+calEvent.id+"&key="+calEvent.id+"&start_time="+calEvent.start.format()+"&end_time="+calEvent.end.format());
            }
        },
        eventAfterAllRender: function() {
            __adianti_process_popover();
        }
    });
}


/**
 * Funções customizadas.
 */

/**
 * Converte um texto para numérico.
 * @param value
 * @returns {number}
 */
function convertToFloatNumber(value) {
    value = value.toString();
    if (value.indexOf('.') !== -1 || value.indexOf(',') !== -1) {
        if (value.indexOf('.') >  value.indexOf(',')) {
            return parseFloat(value.replace(/,/gi,''));
        } else {
            return parseFloat(value.replace(/\./gi,'').replace(/,/gi,'.'));
        }
    } else {
        return parseFloat(value);
    }
};

/**
 * Converte um número para string, aplicado uma formatação numérica.
 * @param number
 * @param decimal
 * @param separatord
 * @param separatort
 * @returns {string}
 */
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


function validaCepDT(valor,formName) {

    let form;
    let req;

    if (valor){
        strvalor = valor.value; 
        cep = strvalor.toString();
        form = formName;
     }

    let url = 'https://viacep.com.br/ws/' + cep.replace(/\D/g, '') + '/json/';
    let oReq = new XMLHttpRequest();

    oReq.open("GET", url, true);
    
    oReq.onload = function(e) {
        
        let response = JSON.parse(oReq.responseText);
    
        document[form].detail_logradouro.value = response.logradouro;
        document[form].detail_bairro.value = response.bairro;
        document[form].detail_cidade.value = response.localidade;
        document[form].detail_uf.value = response.uf;
        document[form].detail_codMuni.value = response.ibge;
    }
    oReq.send();    

}

function validaCep(valor,formName) {

    let form;
    let req;

    if (valor){
        strvalor = valor.value; 
        cep = strvalor.toString();
        form = formName;
     }

    let url = 'https://viacep.com.br/ws/' + cep.replace(/\D/g, '') + '/json/';
    let oReq = new XMLHttpRequest();

    oReq.open("GET", url, true);
    
    oReq.onload = function(e) {
        
        let response = JSON.parse(oReq.responseText);
    
        document[form].logradouro.value = response.logradouro;
        document[form].bairro.value = response.bairro;
        document[form].cidade.value = response.localidade;
        document[form].uf.value = response.uf;
        document[form].codMuni.value = response.ibge;
    }
    oReq.send();    

}


function validaCpfCnpj(valor,formName) {

    let form;

    if (valor){
        strvalor = valor.value; 
        val = strvalor.toString();
        form = formName;
     }

    if (val.length == 14) {
        var cpf = val.trim();
     
        cpf = cpf.replace(/\./g, '');
        cpf = cpf.replace('-', '');
        cpf = cpf.split('');
        
        var v1 = 0;
        var v2 = 0;
        var aux = false;
        
        for (var i = 1; cpf.length > i; i++) {
            if (cpf[i - 1] != cpf[i]) {
                aux = true;   
            }
        } 
        
        if (aux == false) {
            document[form].cpf_cnpj.focus();
            document[form].cpf_cnpj.style.backgroundColor = '#FFCCCC'; 
        } 
        
        for (var i = 0, p = 10; (cpf.length - 2) > i; i++, p--) {
            v1 += cpf[i] * p; 
        } 
        
        v1 = ((v1 * 10) % 11);
        
        if (v1 == 10) {
            v1 = 0; 
        }
        
        if (v1 != cpf[9]) {
            document[form].cpf_cnpj.focus();
            document[form].cpf_cnpj.style.backgroundColor = '#FFCCCC'; 
        } 
        
        for (var i = 0, p = 11; (cpf.length - 1) > i; i++, p--) {
            v2 += cpf[i] * p; 
        } 
        
        v2 = ((v2 * 10) % 11);
        
        if (v2 == 10) {
            v2 = 0; 
        }
        
        if (v2 != cpf[10]) {
            document[form].cpf_cnpj.focus();
            document[form].cpf_cnpj.style.backgroundColor = '#FFCCCC'; 
        } else {   
            document[form].cpf_cnpj.style.backgroundColor = '#CCFFCC'; 
        }
    } else if (val.length == 18) {
        var cnpj = val.trim();
        
        cnpj = cnpj.replace(/\./g, '');
        cnpj = cnpj.replace('-', '');
        cnpj = cnpj.replace('/', ''); 
        cnpj = cnpj.split(''); 
        
        var v1 = 0;
        var v2 = 0;
        var aux = false;
        
        for (var i = 1; cnpj.length > i; i++) { 
            if (cnpj[i - 1] != cnpj[i]) {  
                aux = true;   
            } 
        } 
        
        if (aux == false) {  
            document[form].cpf_cnpj.focus();
            document[form].cpf_cnpj.style.backgroundColor = '#FFCCCC';
        }
        
        for (var i = 0, p1 = 5, p2 = 13; (cnpj.length - 2) > i; i++, p1--, p2--) {
            if (p1 >= 2) {  
                v1 += cnpj[i] * p1;  
            } else {  
                v1 += cnpj[i] * p2;  
            } 
        } 
        
        v1 = (v1 % 11);
        
        if (v1 < 2) { 
            v1 = 0; 
        } else { 
            v1 = (11 - v1); 
        } 
        
        if (v1 != cnpj[12]) {  
            document[form].cpf_cnpj.focus();
            document[form].cpf_cnpj.style.backgroundColor = '#FFCCCC';
        } 
        
        for (var i = 0, p1 = 6, p2 = 14; (cnpj.length - 1) > i; i++, p1--, p2--) { 
            if (p1 >= 2) {  
                v2 += cnpj[i] * p1;  
            } else {   
                v2 += cnpj[i] * p2; 
            } 
        }
        
        v2 = (v2 % 11); 
        
        if (v2 < 2) {  
            v2 = 0;
        } else { 
            v2 = (11 - v2); 
        } 
        
        if (v2 != cnpj[13]) {   
            document[form].cpf_cnpj.focus();
            document[form].cpf_cnpj.style.backgroundColor = '#FFCCCC';
        } else {  
            document[form].cpf_cnpj.style.backgroundColor = '#CCFFCC'; 
        }
    } else {
        document[form].cpf_cnpj.focus();
        document[form].cpf_cnpj.style.backgroundColor = '#FFCCCC';
    }
 }

fwFormatarCpfCnpj = function(e) {
    var s = "";

    if( e )
     {
      s = e.value;
     }
     else
     {
      s = value;
     }
     s = s.replace(/[^0-9]/g,"");
     tam =  s.length;
     if(tam < 12)
     {
         r = s.substring(0,3) + "." + s.substring(3,6) + "." + s.substring(6,9);
         r += "-" + s.substring(9,11);
         if ( tam < 4 )
          s = r.substring(0,tam);
         else if ( tam < 7 )
          s = r.substring(0,tam+1);
         else if ( tam < 10 )
          s = r.substring(0,tam+2);
         else
          s = r.substring(0,tam+3);
        }else{
            r = s.substring(0,2) + "." + s.substring(2,5) + "." + s.substring(5,8);
         r += "/" + s.substring(8,12) + "-" + s.substring(12,14);
         if ( tam < 3 )
          s = r.substring(0,tam);
         else if ( tam < 6 )
          s = r.substring(0,tam+1);
         else if ( tam < 9 )
          s = r.substring(0,tam+2);
         else if ( tam < 13 )
          s = r.substring(0,tam+3);
         else
          s = r.substring(0,tam+4);
        }
     if( e )
     {
      e.value = s;
      return true;
     }
     return s;
};
