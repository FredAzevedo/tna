<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link href="https://code.google.com/apis/maps/documentation/javascript/examples/default.css" rel="stylesheet" type="text/css" />
  <style type="text/css">
    #instructions li {
      color: #666;
      font-style: italic;
    }
    #route {
      list-style-type: upper-alpha;
    }
    #route li {
      cursor: n-resize;
    }
  </style>
</head>
<body onload="initialize()">

  <div id="map_canvas" style="float:left;width:73%;height:100%;"></div>
  <div id="control_panel" style="float:right;width:25%;text-align:left;">
    <div style="padding:10px">

      <ul id="instructions">
        <li>Dê um duplo clique para remover um endereço da rota.</li>
        <li>Arraste os endereços para reordenar os pontos de parada.</li>
      </ul>
      <div class="form-group">
        <label for="usr">Endereço completo:</label>
        <input type="text" class="form-control" id="addr" name="address"></br>
        <input class="btn btn-primary" type="button" id="adic" name="adicionar" value="Adicionar à Rota" />
      </div>
      <ul class="list-group" id="route">
        <li id="1">Rua Frei Miguelinho, 62 Ribeira, Natal-RN</li> 
      </ul>

      <input class="btn btn-primary" type="button" id="trace" name="trace" value="Traçar Rota" />
      <input type="checkbox" id="optimize" value="1"/><br>
      <label for="optimize"><font size=3>Otimizar pontos intermediários</label>
    </div>

  </div>

  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDW7ciTPOPFsKbLWSZ02cK-CnSsICkgeqM&callback=initMap"
    type="text/javascript"></script>
    <!-- <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script> -->
  <script src="https://www.google.com/jsapi" type="text/javascript"></script>
  <script language="Javascript" type="text/javascript">
  //<![CDATA[
    google.load("jquery"  , "1.6.2" );
    google.load("jqueryui", "1.8.16");
  //]]>
  </script>
  <script type="text/javascript">
    var directionDisplay;
    var map;
    var directionsService = new google.maps.DirectionsService();

    function initialize() {
      directionsDisplay = new google.maps.DirectionsRenderer();
      var mapa = new google.maps.LatLng( -5.4742, -35.1234);
       
      var myOptions = {
        zoom: 7,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: mapa
      }
      map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
      directionsDisplay.setMap(map);
    }

    $(function(){
      //Adiciona endereço à lista
      $('#adic').click(function(){
        var ender = $('#addr').val();
        var len   = $('#route').sortable("toArray").length;

        //Verifica se foi digitado um endereço
        if (ender == '') {
          alert('Esqueceu! Preencha o campo de endereço.');
          return false;
        }

        $('#addr').val('').focus();                                      //Limpa endereço e devolve o cursor nele
        var newid = new Date().getTime();                                //hora em milisegundos para criar id único
        $('#route').append('<li id="' + newid + '">' + ender + '</li>'); //Adiciona endereço ao final da lista

        //Vefirica se foi adicionado o último endereço e desabilita o campo
        if (len == 9) {
          $('#addr, #adic').attr('disabled', 'disabled');
        }

      });

      //Habilita embaralhamento da lista
      $('#route').sortable({axis:'y'});

      //exclui endereço no duplo clique
      $('#route').delegate('li', 'dblclick', function(){
        $(this).remove();
        $('#addr, #adic').removeAttr('disabled');
      });

      //Função auxiliar para pegar o texto do id passado
      function getText(id) {
        return $('#'+id).text();
      }

      //Função chamada no clique do botão para traçar a rota
      $('#trace').click(function () {
        var addresses = $('#route').sortable("toArray");
        var len       = addresses.length
        var optmze = $('#optimize:checked').val() == undefined ? false : true;

        //Verifica se tem o mínimo de endereços
        if (len < 2) {
          alert('Se liga... Não existe rota sem pelo menos dois endereços!');
          return false;
        }

        //Verifica se passou o limite de endereços
        if (len > 10) {
          alert('Nada feito, se quiser mais de 10 pontos, tem que pagar pra Google');
          return false;
        }

        var start = getText(addresses[0]);       //Pega o primeiro endereço
        var end   = getText(addresses[len - 1]); //Pega o último endereço
        var waypts = [];
        for (var i = 1; i < len-1; i++) {        //Monta Array com endereços intermediários
            waypts.push({
                location:getText(addresses <?php echo "[","i","]"; ?>),
                stopover:true});
         }

        //Monta objeto com os parâmetro a serem passados pro Google Maps
        var request = {
            origin: start,
            destination: end,
            waypoints: waypts,
            optimizeWaypoints: optmze,
            travelMode: google.maps.DirectionsTravelMode.DRIVING
        };

        //Faz requisição
        directionsService.route(request, function(response, status) {
          if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response); // Se retornar com sucesso, renderiza o mapa
          } else {
            alert('Algo deu errado, verifique os endereços.');
          }
        });
      });

    });

  </script>
</body>
</html>