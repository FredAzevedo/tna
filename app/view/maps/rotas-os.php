<html>
<!-- [main] -->
  <head>
    <style>
      #mapa {
        height:100%;
        width:100%;
      }
    </style>
  </head>
  <body>
    <div id="mapa"></div>
    <!-- <?php echo $_GET['lat'];?>
    <?php echo $_GET['lng'];?> -->
    <script>

      function inicializar() {
        var coordenadas = {lat:<?php echo $_GET['lat'];?>, lng:<?php echo $_GET['lng'];?> };

        var mapa = new google.maps.Map(document.getElementById('mapa'), {
          zoom: 16,
          center: coordenadas 
        });

        var marker = new google.maps.Marker({
          position: coordenadas,
          map: mapa,
          title: 'Destino do Cliente'
        });
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDW7ciTPOPFsKbLWSZ02cK-CnSsICkgeqM&callback=inicializar">
    </script>
  </body>
<!-- [/main] -->
</html>