<?php
/*
Plugin Name: ToolsOnTools Plugin V3
Description: Custom Database Interface
Version: 2.0.0
Author: Allen Jacques
Author URI: http://toolsontools.com/
 */
 
require WP_CONTENT_DIR.'/plugins/tot_plugin_files/plugin_activations.php';
require WP_CONTENT_DIR."/plugins/tot_plugin_files/plugin_code_db.php";
require WP_CONTENT_DIR."/plugins/tot_plugin_files/plugin_code_forms.php";
require WP_CONTENT_DIR."/plugins/tot_plugin_files/plugin_process_forms.php";
require WP_CONTENT_DIR."/plugins/tot_plugin_files/plugin_main_form.php";
require WP_CONTENT_DIR."/plugins/tot_plugin_files/plugin_code_css.php";
require WP_CONTENT_DIR."/plugins/tot_plugin_files/plugin_sms_notifications.php";
// ADDED FOR SMS CAPABILITY
require WP_CONTENT_DIR."/plugins/tot_plugin_files/twilio/Services/Twilio.php";

// NOTE TO SELF. KEEP THIS REGISTER_ACTIVATION_HOOK's IN THE PRIMARY FILE. 
// THEY DONT WORK IN AN INCLUDED FILE. WELCOME TO PWRDPRESS :)

// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'install_plugin_create_records_table'); // used to be records
register_activation_hook(__FILE__,'install_plugin_create_fields_table'); // used to be fields
register_activation_hook(__FILE__,'install_plugin_create_groups_table'); // used to be groups

register_activation_hook(__FILE__,'insert_data'); // inserts initial data into tables

register_deactivation_hook(__FILE__,'db_uninstall'); // uninstalls tables


if(!function_exists(db_css_includes)) {
function db_css_includes(){ // included for styling 
 //	load css specific files
	wp_register_style("db_styling", plugins_url( 'style.css',__FILE__));
	wp_enqueue_style("db_styling");
}}

add_action('wp_head', 'db_css_includes'); //activates the style.css


// ADDED LOGIN/LOGOUT
add_filter( 'wp_nav_menu_items', 'add_loginout_link', 10, 3 );

function add_loginout_link( $items, $args ) {
    if (is_user_logged_in() && $args->theme_location == 'primary-menu') {
        $items .= '<li><a href="'. wp_logout_url() .'">Log Out</a></li>';
    }
    elseif (!is_user_logged_in() && $args->theme_location == 'primary-menu') {
        $items .= '<li><a href="'. site_url('wp-login.php') .'">Log In</a></li>';
        $items .= '<li><a href="' . wp_registration_url() . '" title="' . __('Register') . '">' . __('Register') . '</a>';
    }
    return $items;
}

add_action( 'woo_nav_inside', 'woo_custom_add_searchform', 10 );


if(!function_exists(tot_plugin_js_includes)) {
function tot_plugin_js_includes(){
	//jQuery core
	wp_enqueue_script("jquery");
	wp_register_script("tot_ready", plugins_url("ready.js",__FILE__));
	
	wp_enqueue_script("tot_ready", plugins_url("ready.js",__FILE__));
}}
	
add_action('wp_head', 'tot_plugin_js_includes'); 

?>