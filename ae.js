//script goes here for the map editor
var drawingManager;
var selectedShape;
var colors = ['#1E90FF', '#FF1493', '#32CD32', '#FF8C00', '#4B0082'];
var selectedColor;
var colorButtons = {};
var globalcolor,globalvertices,newShape;
var bounds = new google.maps.LatLngBounds();

function clearSelection() {
if (selectedShape) {
  selectedShape.setEditable(false);
  selectedShape = null;
}
}

function setSelection(shape) {
clearSelection();
selectedShape = shape;
shape.setEditable(true);
selectColor(shape.get('fillColor') || shape.get('strokeColor'));
}

function deleteSelectedShape() {
if (selectedShape) {
  selectedShape.setMap(null);
  setdata_layer('','');
}
}

function selectColor(color) {
globalcolor = selectedColor = color;
for (var i = 0; i < colors.length; ++i) {
  var currColor = colors[i];
  colorButtons[currColor].style.border = currColor == color ? '2px solid #789' : '2px solid #fff';
}

// Retrieves the current options from the drawing manager and replaces the
// stroke or fill color as appropriate.

var polygonOptions = drawingManager.get('polygonOptions');
polygonOptions.fillColor = color;
drawingManager.set('polygonOptions', polygonOptions);
}

function setSelectedShapeColor(color) {
if (selectedShape) {
  if (selectedShape.type == google.maps.drawing.OverlayType.POLYLINE) {
    selectedShape.set('strokeColor', color);
  } else {
    selectedShape.set('fillColor', color);
  }
}
}

function makeColorButton(color) {
var button = document.createElement('span');
button.className = 'color-button';
button.style.backgroundColor = color;
google.maps.event.addDomListener(button, 'click', function() {
  selectColor(color);
  setSelectedShapeColor(color);
  setdata_layer(globalcolor,globalvertices);
});

return button;
}

function buildColorPalette() {
 var colorPalette = document.getElementById('color-palette');
 for (var i = 0; i < colors.length; ++i) {
   var currColor = colors[i];
   var colorButton = makeColorButton(currColor);
   colorPalette.appendChild(colorButton);
   colorButtons[currColor] = colorButton;
 }
 selectColor(colors[0]);
}

function setdata_layer(global_color,global_vertices){
    //return false;
    if(global_color=="" || global_vertices==''){
      document.getElementById('data_layer').value='';
      document.getElementById('addMap_canvas_data').innerHTML='Map Data:: <br><br>No Area selected!!';
      globalvertices=globalcolor="";
    }else{
        var verticesArray=new Array();
        // Iterate over the vertices.
          for (var i =0; i < global_vertices.getLength(); i++) {
            var xy = global_vertices.getAt(i);
            verticesArray.push({'latitude':xy.lat(),'longitude':xy.lng()});
          }
          verticesArray.push({'color_code':selectedColor});
          var oops=JSON.stringify(verticesArray);
          document.getElementById('data_layer').value=oops;
          document.getElementById('addMap_canvas_data').innerHTML='Map Data:: <br><br>'+oops;
    }
}

function initialize() {
var styles=[{featureType:"administrative",stylers:[{visibility:"on"}]},{featureType:"poi",stylers:[{visibility:"simplified"}]},{featureType:"road",elementType:"labels",stylers:[{visibility:"simplified"}]},{featureType:"water",stylers:[{visibility:"simplified"}]},{featureType:"transit",stylers:[{visibility:"simplified"}]},{featureType:"landscape",stylers:[{visibility:"simplified"}]},{featureType:"road.highway",stylers:[{visibility:"on"}]},{featureType:"road.local",stylers:[{visibility:"on"}]},{featureType:"road.highway",elementType:"geometry",stylers:[{visibility:"on"}]},{featureType:"water",stylers:[{color:"#84afa3"},{lightness:52}]},{stylers:[{saturation:-17},{gamma:0.36}]},{featureType:"transit.line",elementType:"geometry",stylers:[{color:"#3f518c"}]}];

  var mapOptions = {
    zoom: 9,
    center: new google.maps.LatLng(-33.6682982,-70.363372),
    disableDefaultUI: false,
    zoomControl: true,
    mapTypeId: google.maps.MapTypeId.TERRAIN
  };

  var map = new google.maps.Map(document.getElementById('addMap_canvas'),
      mapOptions);
  //map.setOptions({styles: styles});
  
  // Create the search box and link it to the UI element.
  var panel = /** @type {HTMLInputElement} */(
      document.getElementById('panel'));
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(panel);
  
  // Create the search box and link it to the UI element.
  var color_palette = /** @type {HTMLInputElement} */(
      document.getElementById('color-palette'));
  map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(color_palette);
  
  // Create the search box and link it to the UI element.
  var input = /** @type {HTMLInputElement} */(
      document.getElementById('pac-input'));
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
  
  var searchBox = new google.maps.places.SearchBox(
            /** @type {HTMLInputElement} */(input));
  
  var defaultBounds = new google.maps.LatLngBounds();
            
  // [START region_getplaces]
  // Listen for the event fired when the user selects an item from the
  // pick list. Retrieve the matching places for that item.
  google.maps.event.addListener(searchBox, 'places_changed', function() {
    var places = searchBox.getPlaces();

    if (places.length == 0) {
      return;
    }

    // For each place, get the icon, place name, and location.
    bounds = new google.maps.LatLngBounds();
    for (var i = 0, place; place = places[i]; i++) {  
      bounds.extend(place.geometry.location);
    }

    map.fitBounds(bounds);
  });
  // [END region_getplaces]

  // Bias the SearchBox results towards places that are within the bounds of the
  // current map's viewport.
  google.maps.event.addListener(map, 'bounds_changed', function() {
    var bounds = map.getBounds();
    searchBox.setBounds(bounds);
  });

var polyOptions = {
  strokeWeight: 0,
  fillOpacity: 0.45,
  editable: true
};
// Creates a drawing manager attached to the map that allows the user to draw
// markers, lines, and shapes.
drawingManager = new google.maps.drawing.DrawingManager({
  drawingControlOptions:{
    position: google.maps.ControlPosition.BOTTOM_LEFT,
    drawingModes: [
          google.maps.drawing.OverlayType.POLYGON
        ]
  },
  drawingMode: google.maps.drawing.OverlayType.POLYGON,
  polygonOptions: polyOptions,
  map: map
});

/*
google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
    if (e.type != google.maps.drawing.OverlayType.MARKER) {
    // Switch back to non-drawing mode after drawing a shape.
    drawingManager.setDrawingMode(null);

    // Add an event listener that selects the newly-drawn shape when the user
    // mouses down on it.
    newShape = e.overlay;
    newShape.type = e.type;
    google.maps.event.addListener(newShape, 'click', function() {
      setSelection(newShape);
    });
    setSelection(newShape);
    
    google.maps.event.addListener(newShape.getPath(), 'insert_at', function(index, obj) {
           //polygon object: yourPolygon
           return setdata_layer(globalcolor,globalvertices);
    });
    google.maps.event.addListener(newShape.getPath(), 'set_at', function(index, obj) {
           //polygon object: yourPolygon
           return setdata_layer(globalcolor,globalvertices);
    });
    
    //
      if (e.type == google.maps.drawing.OverlayType.POLYGON) {
        // Since this polygon has only one path, we can call getPath()
        // to return the MVCArray of LatLngs.
        var vertices = e.overlay.getPath();
        globalvertices=vertices;
        setdata_layer(globalcolor,globalvertices);
      }
  }
});
*/

// Clear the current selection when the drawing mode is changed, or when the
// map is clicked.
google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
google.maps.event.addListener(map, 'click', clearSelection);
google.maps.event.addDomListener(document.getElementById('delete-shape-button'), 'click', deleteSelectedShape);


buildColorPalette();

    //draw shape requested for the editing..
    if(document.getElementById('data_layer').value){
        var layerJSON=document.getElementById('data_layer').value;
        var layerCoords=JSON.parse(layerJSON);
        var coord=[];
        for(var l=0;l<layerCoords.length-1;l++){
            coord[l]=new google.maps.LatLng(layerCoords[l].latitude, layerCoords[l].longitude);
            bounds.extend(new google.maps.LatLng(layerCoords[l].latitude, layerCoords[l].longitude));
        }
        
        // Construct the polygon.
          newShape = new google.maps.Polygon({
            paths: coord,
            draggable: true,
            editable: true,                       
            strokeColor: '#FFF',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: layerCoords[l].color_code,
            fillOpacity: 0.45          
          });
        
          newShape.setMap(map);
          map.fitBounds(bounds);//set polygon in center of map
          
          //set globals
          globalcolor=layerCoords[l].color_code;
          globalvertices=newShape.getPath();
          newShape.type=google.maps.drawing.OverlayType.POLYGON;
          //setdata_layer(globalcolor,globalvertices);
          
          //set drawing mode at hand tool
            drawingManager.setOptions({
              drawingMode: null
            });
          
          //add edit events
            google.maps.event.addListener(newShape, 'click', function() {
              setSelection(newShape);
            });
            setSelection(newShape);
            
            google.maps.event.addListener(newShape.getPath(), 'insert_at', function(index, obj) {
                   //polygon object: yourPolygon
                   return setdata_layer(globalcolor,globalvertices);
            });
            google.maps.event.addListener(newShape.getPath(), 'set_at', function(index, obj) {
                   //polygon object: yourPolygon
                   return setdata_layer(globalcolor,globalvertices);
            });
    }
}
google.maps.event.addDomListener(window, 'load', initialize);


//form validation starts here..
function validate(){
    //if area has not been selected
    if(!document.getElementById('data_layer').value){
        alert('Please select an area on Map and Save it!');
        window.location.hash = '#addMap_canvas';
        return false;
    }
    return true;
}
