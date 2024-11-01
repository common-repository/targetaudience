<?php
/*
 * Plugin Name:       TargetAudience
 * Plugin URI:        https://de.wordpress.org/plugins/targetaudience
 * Description:       TargetAudience helps you to target your website audience more effective.
 * Version:           1.0
 * Author:            Niels Wagner
 * Author URI:        https://marketerbase.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       targetaudience
 * Domain Path:       /languages
*/

if ( ! defined( 'WPINC' ) ) {
    die;
}

if(!class_exists('WPTA_DB')){
	require_once( 'inc/class-wpta-db.php' );
}

if(!class_exists('WPTA_Table')){
    require_once( 'inc/class-wpta-table.php' );
}

function wpta_install() {
	$db = new WPTA_DB();
	$db->create_table();
}
register_activation_hook( __FILE__, 'wpta_install' );

function wpta_permalinks() {
    if (!is_admin()) {
        if (!class_exists('WPTA_SHORTCODE')) {
            require_once('inc/class-wpta-shortcode.php');
        }

        $shortcode = new WPTA_SHORTCODE();
    }
}
add_action( 'init', 'wpta_permalinks' );

function wpta_add_audience_ajax(){
    check_ajax_referer( 'wpta', 'nonce' );

	$db = new WPTA_DB();

	$result = $db->add(sanitize_text_field($_POST['name']), sanitize_text_field($_POST['alternative_1']), sanitize_text_field($_POST['alternative_2']));
    esc_html(die(json_encode($result)));
}
add_action( 'wp_ajax_wpta_add_audience', 'wpta_add_audience_ajax' );

function wpta_get_table_html_ajax(){
    check_ajax_referer( 'wpta', 'nonce' );

	$audiencesTable = new WPTA_Table();
	$audiencesTable->prepare_items();
	$audiencesTable->display();
	die();
}
add_action( 'wp_ajax_wpta_get_table_html', 'wpta_get_table_html_ajax');

function wp_wpta_textdomain() {
	load_plugin_textdomain( 'targetaudience', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'wp_wpta_textdomain');

include('inc/wpta-admin.php');