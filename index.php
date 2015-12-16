<?php
/*********************************************************************************
 * Plugin Name: CSV Import
 * Description: A plugin that helps to import the data's from a CSV file.
 * Version: 1.0
 * Author: smackcoders.com
 * Text Domain: csv-import
 * Domain Path: /languages
 * Plugin URI: http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html
 * Author URI: http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html
 *
 * CSV-Import is a Tool for importing CSV for the Wordpress
 * plugin developed by Smackcoders. Copyright (C) 2014 Smackcoders.
 *
 * CSV-Import is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License version 3
 * as published by the Free Software Foundation with the addition of the
 * following permission added to Section 15 as permitted in Section 7(a): FOR
 * ANY PART OF THE COVERED WORK IN WHICH THE COPYRIGHT IS OWNED BY 
 * CSV-Import, CSV-Import DISCLAIMS THE WARRANTY OF NON
 * INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * CSV-Import is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program; if not, see http://www.gnu.org/licenses or write
 * to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA 02110-1301 USA.
 *
 * You can contact Smackcoders at email address info@smackcoders.com.
 *
 * The interactive user interfaces in original and modified versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License
 * version 3, these Appropriate Legal Notices must retain the display of the
 * CSV-Import copyright notice. If the display of the logo is
 * not reasonably feasible for technical reasons, the Appropriate Legal
 * Notices must display the words
 * "Copyright Smackcoders. 2015. All rights reserved".
 ********************************************************************************/


if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

$get_debug_mode = get_option('csvimportsettings');
if (isset($get_debug_mode['debug_mode']) && $get_debug_mode['debug_mode'] != 'enable_debug') {
	error_reporting(0);
	ini_set('display_errors', 'Off');
}

@ob_start();
add_action('init', 'Start_csvimp_Session', 1);
add_action('wp_logout', 'End_csvimp_Session');
add_action('wp_login', 'End_csvimp_Session');

/**
 * To Start Session
 */
function Start_csvimp_Session() {
	if (!session_id()) {
		session_start();
	}
}

/**
 * To Destroy session
 */
function End_csvimp_Session() {
	session_destroy();
}

if (empty($GLOBALS['wp_rewrite'])) {
       $GLOBALS['wp_rewrite'] = new WP_Rewrite();
}

define('WP_CONST_CSV_IMP_URL', 'http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html');
define('WP_CONST_CSV_IMP_NAME', 'CSV Import');
define('WP_CONST_CSV_IMP_SLUG', 'csv-import');
define('WP_CONST_CSV_IMP_SETTINGS', 'CSV Import');
define('WP_CONST_CSV_IMP_VERSION', '1.0');
define('WP_CONST_CSV_IMP_DIR', WP_PLUGIN_URL . '/' . WP_CONST_CSV_IMP_SLUG . '/');
define('WP_CONST_CSV_IMP_DIRECTORY', plugin_dir_path(__FILE__));
define('CSVIMP_PLUGIN_BASE', WP_CONST_CSV_IMP_DIRECTORY);

if (!class_exists('SkinnyControllerCsvImportFree')) {
	require_once('lib/skinnymvc/controller/SkinnyController.php');
}

add_action('plugins_loaded', 'load_csvimp_langfiles');

function load_csvimp_langfiles() {
	$csv_importer_dir = dirname(plugin_basename(__FILE__)) . '/languages/';
	load_plugin_textdomain('csv-import', false, $csv_importer_dir);
}

require_once('plugins/class.inlineimages.php');
require_once('includes/WPcsvImporter_includes_helper.php');

# Activation & Deactivation 
register_activation_hook(__FILE__, array('WPcsvImport_includes_helper', 'activate'));
register_deactivation_hook(__FILE__, array('WPcsvImport_includes_helper', 'deactivate'));

function action_csvimport_admin_menu() {
	if (is_multisite()) {
		if (current_user_can('administrator')) {
			add_menu_page(WP_CONST_CSV_IMP_SETTINGS, WP_CONST_CSV_IMP_NAME, 'manage_options', __FILE__, array('WPcsvImport_includes_helper', 'output_fd_page'), WP_CONST_CSV_IMP_DIR . "images/icon.png");
		} else {
			if (current_user_can('author') || current_user_can('editor')) {
				$HelperObj = new WPcsvImport_includes_helper();
				$settings = $HelperObj->getSettings();
				if (isset($settings['enable_plugin_access_for_author']) && $settings['enable_plugin_access_for_author'] == 'enable_plugin_access_for_author') {
					add_menu_page(WP_CONST_CSV_IMP_SETTINGS, WP_CONST_CSV_IMP_NAME, '2', __FILE__, array('WPcsvImport_includes_helper', 'output_fd_page'), WP_CONST_CSV_IMP_DIR . "images/icon.png");
				}
			}
		}
	} else {
		if (current_user_can('administrator')) {
			add_menu_page(WP_CONST_CSV_IMP_SETTINGS, WP_CONST_CSV_IMP_NAME, 'manage_options', __FILE__, array('WPcsvImport_includes_helper', 'output_fd_page'), WP_CONST_CSV_IMP_DIR . "images/icon.png");
		} else {
			if (current_user_can('author') || current_user_can('editor')) {
				$HelperObj = new WPcsvImport_includes_helper();
				$settings = $HelperObj->getSettings();
				if (isset($settings['enable_plugin_access_for_author']) && $settings['enable_plugin_access_for_author'] == 'enable_plugin_access_for_author') {
					add_menu_page(WP_CONST_CSV_IMP_SETTINGS, WP_CONST_CSV_IMP_NAME, '2', __FILE__, array('WPcsvImport_includes_helper', 'output_fd_page'), WP_CONST_CSV_IMP_DIR . "images/icon.png");
				}
			}
		}
	}
}

add_action("admin_menu", "action_csvimport_admin_menu");

function action_csvimport_admin_init() {
	if (isset($_REQUEST['page']) && ($_REQUEST['page'] == 'csv-import/index.php' || $_REQUEST['page'] == 'page')) {

		wp_register_script('csv-import-js', plugins_url('/js/csv-import-free.js', __FILE__));
		wp_enqueue_script('csv-import-js');
		wp_enqueue_style('style', plugins_url('/css/style.css', __FILE__));
		wp_enqueue_style('bootstrap-css', plugins_url('/css/bootstrap.css', __FILE__));
		wp_enqueue_style('ultimate-importer-css', plugins_url('/css/main.css', __FILE__));
		wp_enqueue_style('morris-css', plugins_url('/css/morris.css', __FILE__));
		// For chart js
		wp_register_script('raphael-min-js', plugins_url('/js/raphael-min.js', __FILE__));
                wp_enqueue_script('raphael-min-js');
                wp_register_script('morris-min-js', plugins_url('/js/morris.min.js', __FILE__));
                wp_enqueue_script('morris-min-js');
                wp_register_script('data', plugins_url('/js/dashchart.js', __FILE__));
                wp_enqueue_script('data');

	}
}

add_action('admin_init', 'action_csvimport_admin_init');

// Move Pages above Media
function csvimportfree_change_menu_order($menu_order) {
	return array('index.php', 'edit.php', 'edit.php?post_type=page', 'upload.php', 'csv-import/index.php',);
}

add_filter('custom_menu_order', '__return_true');
add_filter('menu_order', 'csvimportfree_change_menu_order');

function firstcsvImpchart() {
	require_once("modules/dashboard/actions/chartone.php");
	die();
}

add_action('wp_ajax_firstcsvImpchart', 'firstcsvImpchart');

function uploadfile_handle() {
	require_once("lib/jquery-plugins/uploader.php");
	die();
}

add_action('wp_ajax_uploadfile_handle', 'uploadfile_handle');

function secondcsvImpchart() {
	require_once("modules/dashboard/actions/chartone.php");
	die();
}

add_action('wp_ajax_secondcsvImpchart', 'secondcsvImpchart');

function round_csvimp_chart() {
	global $wpdb;
	ob_flush();
	$csvImpObj = new WPcsvImport_includes_helper();
	$content = "<form name='csvimp_piechart'> <div id ='csvimp_pieStats' style='height:250px;'>";
	$csvImpObj->piechart();
	$content .= "</div></form>";
	echo $content; 
}

function line_csvimp_Stats() {
	global $wpdb;
	ob_flush();
	$csvImpObj = new WPcsvImport_includes_helper();
	$content = "<form name='csvimp_piechart'> <div id ='csvimp_lineStats' style='height:250px'>";
	$csvImpObj->getStatsWithDate();
	$content .= "</div></form>";
	echo $content;
}


function csvimport_add_dashboard_widgets() {
	wp_enqueue_script('dashpiechart', plugins_url('/js/dashchart-widget.js', __FILE__));
	wp_enqueue_style('morriscss-csvimp', plugins_url('/css/morris.css', __FILE__));
	wp_enqueue_script('csvimpraphael-js', plugins_url('/js/raphael-min.js', __FILE__));
        wp_enqueue_script('csvimpmorris-js', plugins_url('/js/morris.min.js', __FILE__));
	wp_add_dashboard_widget('csvimport_dashboard_piechart', 'CSV-Import-Statistics', 'round_csvimp_chart', $screen = get_current_screen(), 'advanced', 'high');
	wp_add_dashboard_widget('csvimport_dashboard_linechart', 'CSV-Import-Activity', 'line_csvimp_Stats', $screen = get_current_screen(), 'advanced', 'high');
}

add_action('wp_dashboard_setup', 'csvimport_add_dashboard_widgets');

/**
 * To Process the Import
 */
function csvImpByRequest() {
	require_once("templates/import.php");
	die;
}

add_action('wp_ajax_csvImpByRequest', 'csvImpByRequest');

/**
 *To translate the alert strings
 */
function trans_csvimp_alertstr() {
	if (isset($_POST['alertmsg'])) {
		echo __($_POST['alertmsg'], 'csv-import');
	}
	die();

}

add_action('wp_ajax_trans_csvimp_alertstr', 'trans_csvimp_alertstr');

/**
 *
 */
function add_customfd() {
	require_once("templates/Addcustomfields.php");
	die;
}

add_action('wp_ajax_add_customfd', 'add_customfd');

