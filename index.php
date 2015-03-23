<?php 
require_once('connect.php');
$qry="SELECT * FROM `data`,device,device_type,soil_types WHERE data.device_id=device.device_id and device.device_type_id=device_type.device_type_id AND soil_types.soil_type_id=data.soil_type_id";
$result = $mysqli->query($qry) or die($mysqli->error.' '.$qry);
if($result->num_rows<=0){$msg='No matched items in the database!!!';$items='NOT_FOUND';}
else{
    $items='FOUND';$kml="";
    while($item=$result->fetch_assoc()){
        
        $polygon="polygon_".$item['data_id'];
        $kml.="var $polygon;";
        
        $kml.="
    // Define the LatLng coordinates for the polygon.
      var Coords_".$item['data_id']." = [";
      $coords=json_decode($item['data_layer']);
      $geos=$bounds="";
      for($i=0;$i<count($coords)-1;$i++){
        $geos.='new google.maps.LatLng('.$coords[$i]->latitude.', '.$coords[$i]->longitude.'),';
        $bounds.='bounds.extend(new google.maps.LatLng('.$coords[$i]->latitude.', '.$coords[$i]->longitude.'));';
      }
       
      $kml.=rtrim($geos,',')."];";
      
      $kml.=$bounds."      
    //console.log(Coords_".$item['data_id'].");
      // Construct the polygon.
      ".$polygon." = new google.maps.Polygon({
        paths: Coords_".$item['data_id'].",
        strokeColor: '#FFF',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '".$coords[$i]->color_code."',
        fillOpacity: 0.35
      });
    
      ".$polygon.".setMap(map);
    
      // Add a listener for the click event.
      google.maps.event.addListener(".$polygon.", 'click', function(event){
        /*// Replace the info window's content and position.*/";
        $kml.="var contentString='<table><tr><td>Device Name:</td><td>".$item['device_name']."</td></tr><tr><td>Temperature:</td><td>".$item['temperature']."</td></tr><tr><td>Humidity:</td><td>".$item['humidity']."</td></tr><tr><td>Acidity:</td><td>".$item['acidity']."</td></tr><tr><td>Soil Type:</td><td>".$item['soil_type_name']."</td></tr><tr><td>Nitrogen:</td><td>".$item['nitrogen']."</td></tr></table><div style=\"margin-top: 7px;padding-TOP: 6px;margin-bottom: 4px;float: right;vertical-align: bottom;\"><a class=\"edit-link\" target=\"_blank\" href=\'area-editor.php?data_id=".$item['data_id']."\'>Edit</a></div>';";$kml.="
          infoWindow.setContent(contentString);
          infoWindow.setPosition(event.latLng);
        
          infoWindow.open(map);
              
      });
      
      google.maps.event.addListener(".$polygon.", 'mouseover', function(event) {
        this.setOptions({fillColor:'".$coords[$i]->color_code."',fillOpacity:'0.8'});
      });
    
      google.maps.event.addListener(".$polygon.", 'mouseout', function(event) {
        this.setOptions({fillColor:'".$coords[$i]->color_code."',fillOpacity:'0.35'});
      });
    
      ";
    }
    $kml.='map.fitBounds(bounds);';
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <title>Soil Project</title>
    
    <link rel="stylesheet" type="text/css"  href="css/smart-forms.css">
    <link rel="stylesheet" type="text/css"  href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css"  href="css/as.css">
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
      tr td:first-child{  font-weight: bold;
  background-color: #8ACB88;
  margin-right: 21px;
  padding: 5px 15px 4px 4px;
  border-bottom: 0.08em solid #FFF;
  color: black;}
      td{ border-bottom: 1px solid #E9F2F7;padding: 5px 0 0 12px;}
table{    border-collapse: collapse;margin-top: 10px;
  font-family: cambria;
  font-size: 122%;}
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=visualization"></script>
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/plugins.js" type="text/javascript"></script>
    <script src="js/scripts.js" type="text/javascript"></script>
    <script>
// This example creates a 2-pixel-wide red polyline showing
// the path of William Kingsford Smith's first trans-Pacific flight between
// Oakland, CA, and Brisbane, Australia.
var bounds = new google.maps.LatLngBounds;
var marker;
var infowindow;
var map;
function initialize() {
  
  var styles=[{featureType:"administrative",stylers:[{visibility:"on"}]},{featureType:"poi",stylers:[{visibility:"simplified"}]},{featureType:"road",elementType:"labels",stylers:[{visibility:"simplified"}]},{featureType:"water",stylers:[{visibility:"simplified"}]},{featureType:"transit",stylers:[{visibility:"simplified"}]},{featureType:"landscape",stylers:[{visibility:"simplified"}]},{featureType:"road.highway",stylers:[{visibility:"on"}]},{featureType:"road.local",stylers:[{visibility:"on"}]},{featureType:"road.highway",elementType:"geometry",stylers:[{visibility:"on"}]},{featureType:"water",stylers:[{color:"#84afa3"},{lightness:52}]},{stylers:[{saturation:-17},{gamma:0.36}]},{featureType:"transit.line",elementType:"geometry",stylers:[{color:"#3f518c"}]}];
  
  var mapOptions = {
    zoom: 15,
    center: new google.maps.LatLng(-33.47666365549, -70.637927055359)
  };

  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
  map.setOptions({styles: styles});
  /*
  var ctaLayer = new google.maps.KmlLayer({
    url: 'http://gmaps-samples.googlecode.com/svn/trunk/ggeoxml/cta.kml'
  });
  ctaLayer.setMap(map);
  
  var kmlLayer = new google.maps.KmlLayer({
    url: 'http://kml-samples.googlecode.com/svn/trunk/kml/Placemark/placemark.kml',
    suppressInfoWindows: true,
    map: map
  });

  google.maps.event.addListener(kmlLayer, 'click', function(kmlEvent) {
    var text = kmlEvent.featureData.description;
    showInContentWindow(text);
  });

  function showInContentWindow(text) {
    var sidediv = document.getElementById('content-window');
    sidediv.innerHTML = text;
  }
  */
  <?php echo $kml; ?>
  
  infoWindow = new google.maps.InfoWindow();   
  
}
  /** @this {google.maps.Polygon} */
function showArrays(event) {

  // Since this polygon has only one path, we can call getPath()
  // to return the MVCArray of LatLngs.
  var vertices = this.getPath();

  var contentString = '<b>Bermuda Triangle polygon</b><br>' +
      'Clicked location: <br>' + event.latLng.lat() + ',' + event.latLng.lng() +
      '<br>';

  // Iterate over the vertices.
  for (var i =0; i < vertices.getLength(); i++) {
    var xy = vertices.getAt(i);
    contentString += '<br>' + 'Coordinate ' + i + ':<br>' + xy.lat() + ',' +
        xy.lng();
  }

  // Replace the info window's content and position.
  infoWindow.setContent(contentString);
  infoWindow.setPosition(event.latLng);

  infoWindow.open(map);
}
google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body class="woodbg">
<!--
	<div id="menu" class="active">
		<div class="inner">
			<h1>Soil.com</h1>
			<a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
			<ul id="menuse">
				<li><a href="/" class="">Home</a></li>
                <li><a href="area-creator.php" class="">Add Area</a></li>
			</ul>
		</div>
	</div>-->
    <div class="smart-wrap">
    	<div class="smart-forms smart-container wrap-2">
        
        	<div class="form-header header-primary" id="menu">
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Soil Project</a></h4>
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
    				<li><a href="index.php" class="">Home</a></li>
                    <li><a href="area-creator.php" class="">Add Area</a></li>
    			</ul>
            </div><!-- end .form-header section -->
   	    
        	<div class="form-body">
            
                <div class="spacer-b30">
                	<div class="tagline"><span>Soil Areas Details </span></div><!-- .tagline -->
                </div>
                
                <div class="section">
                    <div class="map-container">
                        <div id="map-canvas" style="height: 600px;"></div>
                    </div><!-- end .map-container -->                    
                </div><!-- end .section -->
            </div>
         </div>
  </body>
</html>
