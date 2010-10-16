=== Travelmap ===
Contributors: mediascreen
Tags: travel,map,maps,travel blog,travel plan,Google maps,geocoding,location,round the world trip
Requires at least: 2.7
Tested up to: 3.0.1
Stable tag: 1.1

Generates a map of your travels in any post or page based on a list of places.

== Description ==

Travelmap helps you show your travels on a Google map in any post or page. Add places you have visited or plan to visit to show them connected on a world map.

Add arrival dates to automatically show your current position and where you have been so far. Each place can be linked to a custom url - for example a blog post, wikipedia entry or Flickr album. Geocoding is done automatically based on city and country - but if you need to you can override with your own coordinates. 

[See Travelmap in use](http://travelingswede.com/my-travel-map/ "A demo of Travelmap using my travels")
[The plugin homepage](http://travelingswede.com/travelmap/)

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place shortcodes in the posts or pages where you want to show the map or list:
   For the map (height of map in pixels):
   `[travelmap-map height=400]`
   For the list:
   `[travelmap-list]`
1. Add places you want to show. 

== Changelog ==

= 1.1 =
* Fixed race condition that sometimes saved before geocoding
* Improved first use, hides public maps/lists if there is no data
* Updated text on options page

= 1.0 =
* Added drag and drop for locations
* Changed server response code to 401 for nonce missmatch