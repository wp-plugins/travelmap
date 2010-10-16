var travelmap_options = {
	lineColor: {
		past:'#D38DD7', 
		future:'#777777'
	},
	markerColor: {
		past:'#D38DD7',
		present:'#E9A924',
		future:'#777777'
	}
};

	
function initialize() {
	
	travelmap_alphabet = ("ABCDEFGHIJKLMNOPQRSTUVXYZ").split("");
	travelmap_status = 'past';
	var locations = {past: [], future: []};
	var futureLocations = [];
	var markersArray = [];

	var options = {
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	
	travelmap_map = new google.maps.Map(document.getElementById("travelmap"), options);

	var markerBounds = new google.maps.LatLngBounds();

	for (var i = 0; i < travelmap_places.length; i++) {
		
		// Create and place new marker
		var location = new google.maps.LatLng(travelmap_places[i].lat, travelmap_places[i].lng);
		addMarker(location, 
					 travelmap_places[i].city+', '+travelmap_places[i].country, 
					 i, 
					 travelmap_options.markerColor[travelmap_places[i].status]);
	
		// Add position propper line array
		if (travelmap_places[i].status == 'past') {
			locations['past'].push(location);
		} else if (travelmap_places[i].status == 'present') {
			locations['past'].push(location);
			locations['future'].push(location);
		} else {
			locations['future'].push(location);
		}

		// Extend markerBounds with each random point.
		markerBounds.extend(location);

	}

	// Draw two polylines (past and future)
	drawConnector(locations.past, travelmap_options.lineColor.past)
	drawConnector(locations.future, travelmap_options.lineColor.future)
	
	// Set center and zoom depending on marker position
	travelmap_map.fitBounds(markerBounds);
}


function drawConnector(locations, color) {
	
	var connector = new google.maps.Polyline({
		path: locations,
		strokeColor: color,
		strokeOpacity: 0.8,
		strokeWeight: 3
	});
	
	connector.setMap(travelmap_map);
}


function addMarker(location, title, i, color) {
	
	if (i < 25) {
		icon = 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='+travelmap_alphabet[i]+'|'+color.substring(1)+'|FFFFFF';
	} else {
		icon = 'http://chart.apis.google.com/chart?chst=d_map_pin_icon&chld=glyphish_airplane|'+color.substring(1);
	}
	
	marker = new google.maps.Marker({
   	position: location,
   	map: travelmap_map,
		title: title,
		icon: icon
	});
	
	marker.setMap(travelmap_map);
}


initialize();