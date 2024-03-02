<?php
/*
Plugin Name: WP Admin Menu Color
Plugin URI: https://moezbettoumi.fr 
Description: Change the background color of the admin menu.
Version:           1.0.0
Author:            Moez BETTOUMI
Author URI:        https://moezbettoumi.fr
License:           GPL-2.0+
License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
Text Domain:       wp-admin-menu-color
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'AMC_VERSION', '1.0.0' );
define( 'AMC_PATH', plugin_dir_path( __FILE__ ) );
define( 'AMC_URI', plugin_dir_url( __FILE__ ) );

function wpamc_enqueue_custom_scripts() {
    wp_enqueue_style('wpamc-css', plugins_url('assets/styles.css', __FILE__), array(), AMC_VERSION );
}
add_action('admin_enqueue_scripts', 'wpamc_enqueue_custom_scripts');

require_once(plugin_dir_path(__FILE__) . 'inc/admin-page.php');