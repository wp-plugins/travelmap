<?php
/*
Plugin Name: Travelmap
Plugin URI: http://travelingswede.com/travelmap/
Description: Shows your travel plans as a map
Version: 1.1
Author: Marcus Andersson
Author URI: http://travelingswede.com
License: GPL2
*/


function travelmap_show_map( $atts ) {

	// Supported attributes with defaults
	// TODO: Add shortcodes for turning pins/lines on/off and select color for pins/lines
	extract( shortcode_atts( array(
		'height' => '300',
	), $atts ) );


	// Outputs variables neded by later js-files
	$places = travelmap_string_to_array( get_option( 'travelmap_data' ) );
	if ( ! is_array($places) )
		return;

	?>
	<script type="text/javascript">
	var travelmap_places = <?php echo json_encode( $places ); ?>;
	</script>
	<?php

	// Limits script output only to pages where shortcode is used
	global $travel_shortcode_used; 
	$travel_shortcode_used = true;

	// Returns the html required for the map
	$map = '<div id="travelmap" style="height:' . $height . 'px; background:#FFFFFF; margin-bottom:1.5em;"></div>';
	return $map;
}


function travelmap_show_list() {

	$places = travelmap_string_to_array( get_option( 'travelmap_data' ) );
	$i = 0;
	$letters = 'ABCDEFGHIJKLMNOPQRSTUVXYZ';	
	$list = '<tr><th></th><th>Destination</th><th>Arrival</th></tr>';
	
	if ( ! is_array($places) )
		return;
		
	foreach ( $places as $place ) {
		
		$printdate = ( !empty( $place['arrival'] ) ) ? date_i18n( "F j, Y", strtotime( $place['arrival'] ) ) : '-';
		
		if ( !empty( $place['url'] ) ) {
			$printplace = '<a href="' . $place['url'] . '">' . $place['city'] . ', ' . $place['country'] . '</a>';
		} else {
			$printplace = $place['city'] . ', ' . $place['country'];
		}
		
		// TODO: write out days in each place
		// Om denna har datum, kolla om i+1 har datum 
		// Ta fram skillnaden mellan båda strtotime som dagar
		
		$list .= '
			<tr class="' . $place['status'] . '">
				<td>' . $letters[$i] . '</td>
				<td>' . $printplace . '</td>
				<td>' . $printdate  . '</td>
			</tr>';
		
		$i++;
	}

	return '<table id="travelmap-list">' . $list . '</table>';
}


function travelmap_print_js() {
	
	global $travel_shortcode_used;
	if ( ! $travel_shortcode_used )
		return;
 
	wp_register_script( 'google-maps', 'http://maps.google.com/maps/api/js?sensor=false', false, false, true );
	wp_print_scripts( 'google-maps');
 
	wp_register_script( 'travelmap', plugins_url( 'travelmap.js', __FILE__ ), false, false, true );
	wp_print_scripts( 'travelmap' );
}



function travelmap_menu() {
	add_options_page( 'Travelmap Options', 'Travelmap', 'manage_options', 'travelmap-options', 'travelmap_options' );
}


function travelmap_admin_init() {
	
	if ( $_GET['page'] !== 'travelmap-options')
		return;
	
	// Include javascript
	wp_enqueue_script( 'jquery-ui-sortable' );
	
	wp_register_script( 'google-maps', 'http://maps.google.com/maps/api/js?sensor=false', false, false, true );
	wp_enqueue_script( 'google-maps');
	
	wp_register_script( 'travelmap_admin', plugins_url( 'travelmap-admin.js', __FILE__ ), false, false, true );
	wp_enqueue_script( 'travelmap_admin' );
	
	// Register settings
	register_setting( 'travelmap_settings', 'travelmap_data', 'travelmap_textarea_to_array' );
}


// Main function for outputing options page
function travelmap_options() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

?>
<div class="wrap">
	<div class="icon32" id="icon-options-general"></div>
	<h2>Travelmap destinations</h2>
	<p>Add locations to show them on your map.</p>
	<p>Only <em>city</em> and <em>country</em> are obligatory.</p>
	<p>To automatically show where you are right now you need to fill in <em>arrival</em> date in the following format <strong>YYYY-MM-DD</strong>.</p>
	<p>If you supply an <em>URL</em>, the city and country in the list will be linked. It needs to be a full URL (start with http://). Use it to link to, for example, a travel report, Wikipedia article or photo album.</p>
	<p>Leave <em>latitude</em> and <em>longitude</em> empty to geocode the location automatically when you save. You should only input your own values if there is something wrong with the geocoding.</p>
	<p>To show your map or list you insert shortcodes in your post or page.<br />
	 For the map (height of map in pixels):<br />
   <code>[travelmap-map height=400]</code><br />
   For the list:<br />
   <code>[travelmap-list]</code></p>
	
	<table id="travelmap-admin-table" class="widefat" cellspacing="0">
		<thead class="<?php echo wp_create_nonce( 'travelmap' );?>">
			<tr>
				<th scope="col" class="handle manage-column"></th>
				<th scope="col" class="city manage-column">City</th>
				<th scope="col" class="country manage-column">Country</th>
				<th scope="col" class="url manage-column">URL</th>
				<th scope="col" class="arrival manage-column">Arrival</th>
				<th scope="col" class="lat manage-column">Latitude</th>
				<th scope="col" class="lng manage-column">Longitude</th>
				<th scope="col" class="buttons manage-column"></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$places = travelmap_string_to_array( get_option( 'travelmap_data' ) );
			if ( is_array($places) ) {
				foreach ( $places as $place ) {
					echo 
					'<tr class="' . $place['status'] . '">
						<td class="handle"><span class="image"></span></td>
						<td class="city">' . $place['city'] . '</td>
						<td class="country">' . $place['country'] . '</td>
						<td class="url">' . $place['url'] . '</td>
						<td class="arrival">' . $place['arrival'] . '</td>
						<td class="lat">' . $place['lat'] . '</td>
						<td class="lng">' . $place['lng'] . '</td>
						<td class="buttons"><a class="button-secondary edit" href="#" title="Edit row">Edit</a> <a class="delete" href="#" title="Delete row">Delete</a></td>
					</tr>';
				}
			}
			?>
			</tbody>
		</table>
	<a id="add-location" class="button-secondary" href="#" title="Add row for new location">Add location</a>
	</div>
<?php
}


function travelmap_ajax_save() {
	
	// Check nonce
	if ( ! wp_verify_nonce($_POST['nonce'], 'travelmap') ) {
		header( "Status: 401 Unauthorized" );
		die( "Security check failed" );
	}
	// Save data
	if( update_option( 'travelmap_data', $_POST['places'] ) ) {
		$response = 'updated';
	} else {
		$response = 'unchanged';	
	}

	// response output
	header( "Content-Type: text/html" );
	echo $response;
	exit;
}


function travelmap_string_to_array( $input ) {
	$input = trim( $input, " ;\n" );
	if ( empty($input) ) 
		return false;
	$rows = explode( ";", $input );

	foreach ( $rows as $row ) {
		$row = trim( $row, " ;," );
		list( $data['city'], $data['country'], $data['url'], $data['arrival'], $data['lat'], $data['lng'] ) = explode( ",", $row );
		$places[] = array_reverse( array_map( "trim", $data ), true );
	}
	$places = travelmap_add_status( $places );
	return $places;
}


function travelmap_add_status( $places, $status = 'past' ) {
	foreach ($places as $place) {
		$i++;
		$place['status'] = $status = travelmap_get_date_status( $places[$i]['arrival'], $status );
		$newPlaces[] = $place;
	}
	return $newPlaces;
}


function travelmap_get_date_status( $date, $prevStatus = 'past') {
	if ($prevStatus == 'past') {
		if (strtotime( $date ) > time() ) {
			$status = 'present';
		} else {
			$status = 'past';
		}
	} else {
		$status = 'future';
	}
	return $status;
}


function travelmap_add_stylesheet() {
	wp_register_style( 'travelmap', travelmap_get_plugin_path() . 'screen.css' );
	wp_enqueue_style( 'travelmap' );
}


function travelmap_get_plugin_path() {
	return WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ); 
}


add_shortcode( 'travelmap-map', 'travelmap_show_map' );
add_shortcode( 'travelmap-list', 'travelmap_show_list' );

add_action( 'wp_print_styles', 'travelmap_add_stylesheet' );
add_action( 'wp_footer', 'travelmap_print_js' );
add_action( 'admin_menu', 'travelmap_menu' );
add_action( 'admin_init', 'travelmap_admin_init' );
add_action( 'admin_print_styles', 'travelmap_add_stylesheet');	

add_action( 'wp_ajax_nopriv_travelmap_ajax_save', 'travelmap_ajax_save' );
add_action( 'wp_ajax_travelmap_ajax_save', 'travelmap_ajax_save' );
?>