<?php
/**
 * Plugin Name: Woocommerce Addon: Custom Coupon
 * Description: This plugin will add a Coupon System.
 * Plugin URI: https://wordpress.org/
 * Version: 1.0
 * Author: WordPress Developer
 * Author URI: https://wordpress.org/
 * Domain Path: /languages
**/
if (!defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once("classes/class-it-functions.php");
include_once("classes/class-it-plugins-api.php");

// Instantiation of the class
new CustomCouponAddon();
new ITPluginAPI();

