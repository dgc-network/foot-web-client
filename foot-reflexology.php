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
include_once dirname( __FILE__ ) . '/includes/class-courses.php';
include_once dirname( __FILE__ ) . '/includes/class-teaches.php';
/*
include_once dirname( __FILE__ ) . '/php-OP_RETURN/OP_RETURN.php';
include_once dirname( __FILE__ ) . '/vendor/autoload.php';
include_once dirname( __FILE__ ) . '/build/gen/GPBMetadata/PikePayload.php';
include_once dirname( __FILE__ ) . '/build/gen/GPBMetadata/PikeState.php';
include_once dirname( __FILE__ ) . '/build/gen/Agent.php';
include_once dirname( __FILE__ ) . '/build/gen/AgentList.php';
include_once dirname( __FILE__ ) . '/build/gen/PikePayload.php';
include_once dirname( __FILE__ ) . '/build/gen/PikePayload/Action.php';
include_once dirname( __FILE__ ) . '/build/gen/CreateAgentAction.php';
include_once dirname( __FILE__ ) . '/build/gen/UpdateAgentAction.php';
include_once dirname( __FILE__ ) . '/build/gen/KeyValueEntry.php';
*/

?>