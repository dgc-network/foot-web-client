<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
global $wpdb;
//$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}course_lecturers_witnesses" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}user_course_learnings" );
//$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}course_learnings" );
//$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}courses" );
/*
$option_name = 'wporg_option';
 
delete_option($option_name);
 
// for site options in Multisite
delete_site_option($option_name);
 
// drop a custom database table
global $wpdb;
//$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mytable");
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}courses" );
*/
?>