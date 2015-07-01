<?php
/*
Plugin Name: TZS Plugin
Plugin URL: http://tzs.webline.kiev.ua
Version: 0.1
Author: Someone
Author URI: http://tzs.webline.kiev.ua
*/

define( 'TZS_TABLE_PREFIX', "tzs_" );
define( 'TZS_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
global $wpdb;
define( 'TZS_SHIPMENT_TABLE', $wpdb->prefix . TZS_TABLE_PREFIX . "shipments" );
define( 'TZS_TRUCK_TABLE', $wpdb->prefix . TZS_TABLE_PREFIX . "trucks" );
define( 'TZS_YAHOO_KEYS_TABLE', $wpdb->prefix . TZS_TABLE_PREFIX . "yahoo_keys" );
define( 'TZS_COUNTRIES_TABLE', $wpdb->prefix . TZS_TABLE_PREFIX . "countries" );
define( 'TZS_REGIONS_TABLE', $wpdb->prefix . TZS_TABLE_PREFIX . "regions" );
define( 'TZS_CITIES_TABLE', $wpdb->prefix . TZS_TABLE_PREFIX . "cities" );
define( 'TZS_CITY_IDS_TABLE', $wpdb->prefix . TZS_TABLE_PREFIX . "city_ids" );
// KSK - add table for products & auctions
define( 'TZS_PRODUCT_TYPES_TABLE', $wpdb->prefix . TZS_TABLE_PREFIX . "product_types" );
define( 'TZS_PRODUCTS_TABLE', $wpdb->prefix . TZS_TABLE_PREFIX . "products" );
define( 'TZS_AUCTIONS_TABLE', $wpdb->prefix . TZS_TABLE_PREFIX . "auctions" );
define( 'TZS_AUCTION_RATES_TABLE', $wpdb->prefix . TZS_TABLE_PREFIX . "auction_rates" );
define( 'TZS_PRODUCT_RATES_TABLE', $wpdb->prefix . TZS_TABLE_PREFIX . "product_rates" );

include_once(TZS_PLUGIN_DIR.'/functions/tzs.globals.php');
include_once(TZS_PLUGIN_DIR.'/functions/tzs.settings.php');
include_once(TZS_PLUGIN_DIR.'/functions/tzs.functions.php');
include_once(TZS_PLUGIN_DIR.'/functions/tzs.yahoo.php');
include_once(TZS_PLUGIN_DIR.'/functions/tzs.trade.functions.php');



/*function tzs_comments_open_filter($open, $post_id) {
	return true;
}*/

function tzs_install () {
  global $wpdb;

  $sql_shipments = "CREATE TABLE " . TZS_SHIPMENT_TABLE . " (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	last_edited datetime DEFAULT NULL,
	user_id bigint(20) UNSIGNED NOT NULL,
	sh_date_from date DEFAULT '0000-00-00' NOT NULL,
	sh_date_to date DEFAULT '0000-00-00' NOT NULL,
	sh_city_from tinytext NOT NULL,
	sh_city_to tinytext NOT NULL,
	from_cid int(4) UNSIGNED DEFAULT NULL,
	from_rid int(4) UNSIGNED DEFAULT NULL,
	from_sid int(4) UNSIGNED DEFAULT NULL,
	to_cid int(4) UNSIGNED DEFAULT NULL,
	to_rid int(4) UNSIGNED DEFAULT NULL,
	to_sid int(4) UNSIGNED DEFAULT NULL,
	sh_descr tinytext NOT NULL,
	sh_weight float(7,2) NOT NULL,
	sh_volume float(7,2) NOT NULL,
	sh_length float(7,2) NOT NULL,
	sh_height float(7,2) NOT NULL,
	sh_width float(7,2) NOT NULL,
	trans_count smallint(2) NOT NULL DEFAULT 1,
	trans_type smallint(2) DEFAULT 0,
	cost tinytext NOT NULL,
	comment tinytext NOT NULL,
	distance mediumint(3) UNSIGNED NOT NULL DEFAULT 0,
	active smallint(1) DEFAULT 1 NOT NULL,
	UNIQUE KEY id (id)
  );";
  
  $sql_trucks = "CREATE TABLE " . TZS_TRUCK_TABLE . " (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	last_edited datetime DEFAULT NULL,
	user_id bigint(20) UNSIGNED NOT NULL,
	tr_date_from date DEFAULT '0000-00-00' NOT NULL,
	tr_date_to date DEFAULT '0000-00-00' NOT NULL,
	tr_city_from tinytext NOT NULL,
	tr_city_to tinytext NOT NULL,
	from_cid int(4) UNSIGNED DEFAULT NULL,
	from_rid int(4) UNSIGNED DEFAULT NULL,
	from_sid int(4) UNSIGNED DEFAULT NULL,
	to_cid int(4) UNSIGNED DEFAULT NULL,
	to_rid int(4) UNSIGNED DEFAULT NULL,
	to_sid int(4) UNSIGNED DEFAULT NULL,
	tr_weight float(7,2) NOT NULL,
	tr_volume float(7,2) NOT NULL,
	tr_length float(7,2) NOT NULL,
	tr_height float(7,2) NOT NULL,
	tr_width float(7,2) NOT NULL,
	trans_count smallint(2) NOT NULL DEFAULT 1,
	trans_type smallint(2) DEFAULT 0,
	tr_type smallint(2) DEFAULT 0,
	cost tinytext NOT NULL,
	comment tinytext NOT NULL,
	distance mediumint(3) UNSIGNED NOT NULL DEFAULT 0,
	active smallint(1) DEFAULT 1 NOT NULL,
	UNIQUE KEY id (id)
  );";
  
  $sql_yahoo_keys = "CREATE TABLE " . TZS_YAHOO_KEYS_TABLE . " (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    time datetime DEFAULT now() NOT NULL,
	last_used datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	appid char(90) NOT NULL,
	UNIQUE KEY id (id)
  );";
  
  //INSERT INTO wp_tzs_yahoo_keys (appid) values ('dj0yJmk9emc1SWFXZnJ2UUxoJmQ9WVdrOWFESm1abkprTjJVbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD1kNQ')

  $sql_countries = "CREATE TABLE " . TZS_COUNTRIES_TABLE . " (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	country_id bigint(20) UNSIGNED NOT NULL,
	list_country tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
	list_regions tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
	code char(2) DEFAULT NULL,
    title_ru varchar(60) DEFAULT NULL,
	title_ua varchar(60) DEFAULT NULL,
	title_en varchar(60) DEFAULT NULL,
	UNIQUE KEY id (id)
  );";
  
  $sql_regions = "CREATE TABLE " . TZS_REGIONS_TABLE . " (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	country_id bigint(20) UNSIGNED NOT NULL,
	region_id bigint(20) UNSIGNED NOT NULL,
    title_ru varchar(150) DEFAULT NULL,
	title_ua varchar(150) DEFAULT NULL,
	title_en varchar(150) DEFAULT NULL,
	UNIQUE KEY id (id)
  );";
  
  $sql_cities = "CREATE TABLE " . TZS_CITIES_TABLE . " (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	country_id bigint(20) UNSIGNED NOT NULL,
	region_id bigint(20) UNSIGNED NOT NULL,
	city_id bigint(20) UNSIGNED NOT NULL,
    title_ru varchar(150) DEFAULT NULL,
	title_ua varchar(150) DEFAULT NULL,
	title_en varchar(150) DEFAULT NULL,
        lat DOUBLE DEFAULT NULL,
        lng DOUBLE DEFAULT NULL,
	UNIQUE KEY id (id)
  );";
  
  $sql_city_ids = "CREATE TABLE " . TZS_CITY_IDS_TABLE . " (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	title varchar(550) DEFAULT NULL,
	ids text DEFAULT NULL,
	UNIQUE KEY id (id)
  );";
  
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql_shipments );
  dbDelta( $sql_trucks );
  dbDelta( $sql_yahoo_keys );
  
  dbDelta( $sql_countries );
  dbDelta( $sql_regions );
  dbDelta( $sql_cities );
  
  dbDelta( $sql_city_ids );
}

register_activation_hook(__FILE__,'tzs_install');


// SHIPMENT
include_once(TZS_PLUGIN_DIR.'/front-end/tzs.shipment.php');
add_shortcode('tzs-new-shipment', 'tzs_front_end_shipment_handler');
add_shortcode('tzs-edit-shipment', 'tzs_front_end_edit_shipment_handler');
add_shortcode('tzs-del-shipment', 'tzs_front_end_del_shipment_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.shipments.php');
add_shortcode('tzs-view-shipments', 'tzs_front_end_shipments_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.view.shipment.php');
add_shortcode('tzs-view-shipment', 'tzs_front_end_view_shipment_handler');

// TRUCK
include_once(TZS_PLUGIN_DIR.'/front-end/tzs.truck.php');
add_shortcode('tzs-new-truck', 'tzs_front_end_truck_handler');
add_shortcode('tzs-edit-truck', 'tzs_front_end_edit_truck_handler');
add_shortcode('tzs-del-truck', 'tzs_front_end_del_truck_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.trucks.php');
add_shortcode('tzs-view-trucks', 'tzs_front_end_trucks_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.view.truck.php');
add_shortcode('tzs-view-truck', 'tzs_front_end_view_truck_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.my.trucks.php');
add_shortcode('tzs-my-trucks', 'tzs_front_end_my_trucks_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.my.shipments.php');
add_shortcode('tzs-my-shipments', 'tzs_front_end_my_shipments_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.distance.calculator.php');
add_shortcode('tzs-length-calculator', 'tzs_front_end_distance_calculator_handler');

// SEARCH
include_once(TZS_PLUGIN_DIR.'/front-end/tzs.search.php');
add_shortcode('tzs-search', 'tzs_front_end_search_handler');

// FEEDBACK
include_once(TZS_PLUGIN_DIR.'/front-end/tzs.feedback.php');
add_shortcode('tzs-feedback', 'tzs_front_end_feedback_handler');

// FOLLWOING
include_once(TZS_PLUGIN_DIR.'/front-end/tzs.following.php');
add_shortcode('tzs-view-following', 'tzs_front_end_following_handler');

//add_filter('comments_open', 'tzs_comments_open_filter', 10, 2);

//***************************************************************
// KSK
//***************************************************************

// PRODUCTS
include_once(TZS_PLUGIN_DIR.'/front-end/tzs.products.php');
add_shortcode('tzs-view-products', 'tzs_front_end_products_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.user.products.php');
add_shortcode('tzs-view-user-products', 'tzs_front_end_user_products_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.my.products.php');
add_shortcode('tzs-my-products', 'tzs_front_end_my_products_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.view.product.php');
add_shortcode('tzs-view-product', 'tzs_front_end_view_product_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.view.products.php');
add_shortcode('tzs-view-productsd', 'tzs_front_end_view_productsd_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.view.firms.php');
add_shortcode('tzs-view-firms', 'tzs_front_end_view_firms_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.product.php');
add_shortcode('tzs-new-product', 'tzs_front_end_product_handler');
add_shortcode('tzs-edit-product', 'tzs_front_end_edit_product_handler');
add_shortcode('tzs-del-product', 'tzs_front_end_del_product_handler');

// AUCTIONS
/*include_once(TZS_PLUGIN_DIR.'/front-end/tzs.auctions.php');
add_shortcode('tzs-view-auctions', 'tzs_front_end_auctions_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.my.auctions.php');
add_shortcode('tzs-my-auctions', 'tzs_front_end_my_auctions_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.view.auction.php');
add_shortcode('tzs-view-auction', 'tzs_front_end_view_auction_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.view.auctions.php');
add_shortcode('tzs-view-auctionsd', 'tzs_front_end_view_auctionsd_handler');

include_once(TZS_PLUGIN_DIR.'/front-end/tzs.auction.php');
add_shortcode('tzs-new-auction', 'tzs_front_end_auction_handler');
add_shortcode('tzs-edit-auction', 'tzs_front_end_edit_auction_handler');
add_shortcode('tzs-del-auction', 'tzs_front_end_del_auction_handler');
*/
// SEARCH
include_once(TZS_PLUGIN_DIR.'/front-end/tzs.search_pr.php');
add_shortcode('tzs-search-pr', 'tzs_front_end_search_pr_handler');

// IMAGES
include_once(TZS_PLUGIN_DIR.'/front-end/tzs.trade.images.php');
add_shortcode('tzs-edit-images-pr', 'tzs_front_end_pr_images_handler');

?>