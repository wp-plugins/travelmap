<?php
if ( !defined('WP_UNINSTAL_PLUGIN') ) {
	exit();
}

// Deletes travelmap options
delete_option('travelmap_data');
?>