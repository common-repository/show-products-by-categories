<?php
	/*
	Plugin Name: Show products by categories
	Plugin URI: https://github.com/alexeydudka
	Description: Plugin for WordPress WooCommerce. For every page you can tune what product must show, while creation page or post you select in existing caregories what must be show. The plugin adds a unit with a list of all existing product categories, page creation and editing of pages and fasting. As well as adding a new widget that you want to add to any existing location for widgets. After selecting the desired category, and add widgets, the page will appear in a random order, only items that are included in the selected categories.
	Version: 2.0.1
	Author: Dudka Alexey
	Author URI: https://github.com/alexeydudka
	License: GPLv3
	*/
	
	define('SHOW_BY_CATEGORIES_DIR', plugin_dir_path(__FILE__));
	define('SHOW_BY_CATEGORIES_URL', plugin_dir_url(__FILE__));
	include_once(SHOW_BY_CATEGORIES_DIR."show-by-categories-functions.php"); 
?>
