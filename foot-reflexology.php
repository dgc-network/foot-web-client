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
include_once dirname( __FILE__ ) . '/includes/class-badges.php';
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
?>