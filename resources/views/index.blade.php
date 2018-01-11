<!DOCTYPE html>
<html lang="en">
<head>
  <title>PeeMap</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="author" content="Marko Šidlovský">
  <meta name="application-name" content="PeeMap">
  <link rel="shortcut icon" href="favicon.ico" />


  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <style>
    /* Set height of the grid so .sidenav can be 100% (adjust if needed) */
    /*.row.content {height: 1500px}*/

    /* Set gray background color and 100% height */
    .sidenav {
      background-color: #f1f1f1;
      height: 100%;
    }
    
    /* Set black background color, white text and some padding */
    footer {
      background-color: #555;
      color: white;
      padding: 15px;
    }
    
    /* On small screens, set height to 'auto' for sidenav and grid */
    @media screen and (max-width: 767px) {
      .sidenav {
        height: auto;
        padding: 15px;
      }
      .row.content {height: auto;} 
    }

    #map {
      height: 775px;
      width: 100%;
    }

    .no-padding {
      padding-left: 0px;
      padding-right: 0px;
    }

    #info {
      text-align: justify;
    }

    .img-logo{
      height:80px;
      margin:10px 0;
    }

    .img-logo2{
      height:150px;
    }

    .same-height {

    }

    #map {
      height:100%
    }

  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row content">
    <div class="col-sm-3 sidenav same-height">
      <h1>PeeMap</h1>
      <p id="info">PeeMap helps you in time you need it the most. </p>
      <h4><small>Discover your next pee time</small></h4>
      <form class="form-group" id="my-form">
        <input type="number" class="form-control" id="age" name="age" placeholder="Age" min="1" max="150" required>
        <input type="number" class="form-control" id="height" name="height" placeholder="Height" min="10" max="280" required>
        <input type="number" class="form-control" id="weight" name="weight" placeholder="Weight" min="10" max="500" required>
        <input type="number" class="form-control" id="consumption" name="consumption" placeholder="litres of water you drink in last 24 hours"  min="0" max="100" required>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>
      <h4><small>Approximately, you will need to go pee in: <span id="response"></span></small></h4>
      <h3>Technologies</h3>
      <div class="row">
        <div class="col-md-6">
          <img class="img-logo" src="img/html5_logo.png" alt="html5 logo"/>
        </div>
        <div class="col-md-6">
          <img class="img-logo" src="img/bootstrap_logo.png" alt="bootstrap logo"/>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <img class="img-logo" src="img/jquery_logo.gif" alt="jquery logo"/>
        </div>
        <div class="col-md-6">
          <img class="img-logo" src="img/laravel_logo.png" alt="laravel logo"/>
        </div>
      </div>

      <h3>Data</h3>
      <a href="http://www.geoportalpraha.cz"><img class="img-logo2" src="img/ipr_logo.png" alt="IPR Praha logo"/></a>
    </div>

    <div class="col-sm-9 no-padding same-height">
      <div id="map"></div>
    </div>
  </div>
</div>

<footer class="container-fluid">
  <p>Copyright &copy; 2018 Marko Šidlovský</p>
</footer>

<script>

    $( document ).ready(function() {
        var heights = $(".same-height").map(function() {
                return $(this).height();
            }).get(),

            maxHeight = Math.max.apply(null, heights);

        $(".same-height").height(maxHeight);
    });

  var position = {
    lat: 50.087811,
    lng: 14.42046
  }
  var map = null;
  var markers = [];
  var infowindows = [];

  function initMap() {

    map = new google.maps.Map(document.getElementById('map'), {
      zoom: 14,
      center: position
    });

    $.ajax({
      url: 'http://opendata.iprpraha.cz/CUR/FSV/FSV_VerejnaWC_b/WGS_84/FSV_VerejnaWC_b.json'
    }).done(function(response) {
      // console.log(response);
      for(var i in response.features){

        var address = response.features[i].properties.ADRESA;
        var price = response.features[i].properties.CENA;
        var id = response.features[i].properties.OBJECTID;
        var name = 'Public toilet N. ' + id;
        var openingHours = response.features[i].properties.OTEVRENO;
        var lat = response.features[i].geometry.coordinates[1];
        var lon = response.features[i].geometry.coordinates[0];

        markers[i] = new google.maps.Marker({
              position: {lat: parseFloat(lat), lng: parseFloat(lon)},
              map: map
            });

        if(!address || 0 === address.trim().length){
            address = 'Note not mentioned';
        }
        if(!price || 0 === price.trim().length){
            price = 'not mentioned';
        }
        if(!openingHours || 0 === openingHours.trim().length){
            openingHours = 'not mentioned';
        }

        var contentString = 
          '<div style="width:100%;float:left">'+
          '<h4>'+name+'</h4>'+
          address+'<br/>'+
          'Price: '+price+'<br/>'+
          'Opening hours: '+openingHours+'<br/>'+
          '</div>';

        infowindows[i] = new google.maps.InfoWindow({
              content: contentString
            });

        google.maps.event.addListener(markers[i], 'click', function(j) {
              return function() {
                  for(var i = 0; i < infowindows.length; i++){
                    infowindows[i].close(map);
                  }
                  infowindows[j].open(map, markers[j]);
              }
        }(i));

      }
    });
  }

  function processForm( e ){
    console.log("Process form");
    $.ajax({
        url: '{{ action('PeeController@calculate') }}',
        dataType: 'json',
        type: 'post',
        contentType: 'application/json',
        data: JSON.stringify( { "age": $('#age').val(), 
          "weight": $('#weight').val(),
          "height": $('#height').val(),
          "consumption": $('#consumption').val(),
            "_token": "{{ csrf_token() }}"}),
        processData: false,
        success: function( data, textStatus, jQxhr ){
            $('#response').html( JSON.stringify( data.value ) + " hours" );
        },
        error: function( jqXhr, textStatus, errorThrown ){
            alert( errorThrown );
        }
    });

    e.preventDefault();
  }

  $('#my-form').submit( processForm );

</script>

<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCE_EDrRNLbxjDFGDyo7_5mORnTJqFVfwg&callback=initMap">
</script>

</body>
</html>
