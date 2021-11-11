<?php

/**
 * Plugin Name: foot-reflexology
 * Plugin URI: https://wordpress.org/plugins/foot-reflexology/
 * Description: The leading web api plugin for pig system by shortcode
 * Author: dgc.network
 * Author URI: https://dgc.network/
 * Version: 1.0.0
 * Requires at least: 4.4
 * Tested up to: 5.2
 * 
 * Text Domain: foot-reflexology
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
//include_once dirname( __FILE__ ) . '/includes/class-badges.php';
include_once dirname( __FILE__ ) . '/includes/class-calendars.php';
include_once dirname( __FILE__ ) . '/includes/class-orders.php';
include_once dirname( __FILE__ ) . '/includes/class-courses.php';
include_once dirname( __FILE__ ) . '/includes/class-certifications.php';
//include_once dirname( __FILE__ ) . '/includes/class-users.php';
include_once dirname( __FILE__ ) . '/vendor/autoload.php';
include_once dirname( __FILE__ ) . '/blockchain/php-OP_RETURN/OP_RETURN.php';
include_once dirname( __FILE__ ) . '/blockchain/php-OP_RETURN/op_return_setting.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/GPBMetadata/Payload.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/CreateCourseAction.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/UpdateCourseAction.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/CreateCourseLearingAction.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/UpdateCourseLearingAction.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/CreateUserCourseLearingAction.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/UpdateUserCourseLearingAction.php';

wp_insert_term( 'Courses', 'product_cat', array(
    'description' => 'Description for category',
    'parent' => 0,
    'slug' => 'courses'
) );

wp_insert_term( 'Certification', 'product_cat', array(
    'description' => 'Description for category',
    'parent' => 0,
    'slug' => 'certification'
) );

/**
 * Load jQuery datepicker.
 *
 * By using the correct hook you don't need to check `is_admin()` first.
 * If jQuery hasn't already been loaded it will be when we request the
 * datepicker script.
 */
function wpse_enqueue_datepicker() {
    // Load the datepicker script (pre-registered in WordPress).
    wp_enqueue_script( 'jquery-ui-datepicker' );

    // You need styling for the datepicker. For simplicity I've linked to the jQuery UI CSS on a CDN.
    wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
    wp_enqueue_style( 'jquery-ui' );  
}
add_action( 'wp_enqueue_scripts', 'wpse_enqueue_datepicker' );
?>
