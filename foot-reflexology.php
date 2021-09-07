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
add_shortcode( 'course_shortcode', 'course_shortcode_callback' );
function course_shortcode_callback() {

    //remove_courses_table();
    create_courses_table();

    //$AgentList = new AgentList();
    //$Agent = new Agent();
    
    if( isset($_POST['edit_mode']) ) {

        //$agents = $AgentList->getAgents();
/*
        foreach ($courses as $index => $course) {
            if ($_POST['_item']=='edit_'.$index) {
                $PublicKey = $agents[$index]->getPublicKey();
                $KeyValueEntries = $agents[$index]->getMetadata();
                foreach ($KeyValueEntries as $KeyValueEntry)
                if ($KeyValueEntry->getKey()=='email') 
                    $LoginName = $KeyValueEntry->getValue();
            }
        }
*/
        global $wpdb;
        $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}courses WHERE course_id = {$_POST['_course_id']}", OBJECT );
        $output  = '<form method="post">';
        $output .= '<figure class="wp-block-table"><table><tbody>';
        $output .= '<tr><td>'.'Course ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_course_id" value="'.$row->course_id.'"></td></tr>';
        $output .= '<tr><td>'.'Course Name:'.'</td><td><input style="width: 100%" type="text" name="_course_name" value="'.$row->course_name.'"></td></tr>';
        $output .= '</tbody></table></figure>';

        $output .= '<div class="wp-block-buttons">';
        $output .= '<div class="wp-block-button">';
        $output .= '<input class="wp-block-button__link" type="submit" value="Create" name="create_action">';
        if( $_POST['edit_mode']=='Update' ) {
            $output .= '<input class="wp-block-button__link" type="submit" value="Update" name="update_action">';
        }
        $output .= '</div>';
        $output .= '<div class="wp-block-button">';
        $output .= '<input class="wp-block-button__link" type="submit" value="Cancel"';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</form>';
    
        return $output;

    }
    
    if( isset($_POST['create_action']) ) {

        $metakey   = 'Funny Phrases';
        $metavalue = "WordPress' database interface is like Sunday Morning: Easy.";
 
        $wpdb->query(
            $wpdb->prepare(
                "
                INSERT INTO {$wpdb->prefix}courses
                ( course_id, course_name )
                VALUES ( %d, %s )
                ",
                array(
                    $_POST['_course_id'],
                    $_POST['_course_name'],
                )
            )
        );

        $Roles = array();
        $KeyValueEntries = array();
/*
        $KeyValueEntry = new KeyValueEntry();
        $KeyValueEntry->setKey('email');
        $KeyValueEntry->setValue($_POST['_LoginName']);
        $KeyValueEntries[]=$KeyValueEntry;

        $CreateAgentAction = new CreateAgentAction();
        $CreateAgentAction->setOrgId($_GET['_OrgId']);
        $CreateAgentAction->setPublicKey($_POST['_PublicKey']);
        $CreateAgentAction->setActive($_GET['_Active']);
        $CreateAgentAction->setRoles($Roles);
        $CreateAgentAction->setMetadata($KeyValueEntries);

        $send_data = $CreateAgentAction->serializeToString();
        $send_address = 'DFcP5QFjbYtfgzWoqGedhxecCrRe41G3RD';
        $private_key = 'L44NzghbN6UD737kG6ukfdCq6BXyyTY2W15UkNhHnBff6acYWtsZ';
        $send_amount = 0.001;
    
        try {
            $agents = $AgentList->getAgents();
            $Agent->mergeFromString($send_data);
            $agents[] = $Agent;
            $AgentList->setAgents($agents);
            //$send_data = $AgentList->serializeToString();
        } catch (Exception $e) {
            // Handle parsing error from invalid data.
            // ...
        }
*/        
/*
	    $result = OP_RETURN_send($send_address, $send_amount, $send_data);
	
	    if (isset($result['error']))
		    $result_output = 'Error: '.$result['error']."\n";
	    else
            $result_output = 'TxID: '.$result['txid']."\nWait a few seconds then check on: http://coinsecrets.org/\n";
*/
    
    }

    if( isset($_POST['update_action']) ) {

        $Roles = array();
        $KeyValueEntries = array();
/*
        $KeyValueEntry = new KeyValueEntry();
        $KeyValueEntry->setKey('email');
        $KeyValueEntry->setValue($_GET['_Name']);
        $KeyValueEntries[]=$KeyValueEntry;

        $UpdateAgentAction = new UpdateAgentAction();
        $UpdateAgentAction->setOrgId($_GET['_OrgId']);
        $UpdateAgentAction->setPublicKey($_GET['_PublicKey']);
        $UpdateAgentAction->setActive($_GET['_Active']);
        $UpdateAgentAction->setRoles($Roles);
        $UpdateAgentAction->setMetadata($KeyValueEntries);

        $send_data = $UpdateAgentAction->serializeToString();
        $send_address = 'DFcP5QFjbYtfgzWoqGedhxecCrRe41G3RD';
        $private_key = 'L44NzghbN6UD737kG6ukfdCq6BXyyTY2W15UkNhHnBff6acYWtsZ';
        $send_amount = 0.001;
    
        try {
            $agents = $AgentList->getAgents();
            $Agent->mergeFromString($send_data);
            foreach ( $agents as $agent ){

            }
            //$agents[] = $Agent;
            $AgentList->setAgents($agents);
            //$send_data = $AgentList->serializeToString();
        } catch (Exception $e) {
            // Handle parsing error from invalid data.
            // ...
        }

	    $result = OP_RETURN_send($send_address, $send_amount, $send_data);
	
	    if (isset($result['error']))
		    $result_output = 'Error: '.$result['error']."\n";
	    else
            $result_output = 'TxID: '.$result['txid']."\nWait a few seconds then check on: http://coinsecrets.org/\n";
*/
        
    }


    global $wpdb;
    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}courses", OBJECT );
    
    $output  = '<form method="post">';
    $output .= '<figure class="wp-block-table"><table><tbody>';
    $output .= '<tr><td>Course ID</td><td>Course Name</td><td></td><td></td></tr>';

    //$metadata = '';
    //$agents = $AgentList->getAgents();
    foreach ($results as $index => $result) {
/*
        $PublicKey = $agents[$index]->getPublicKey();
        $KeyValueEntries = $agents[$index]->getMetadata();
        foreach ($KeyValueEntries as $KeyValueEntry)
            if ($KeyValueEntry->getKey()=='email') 
                $LoginName = $KeyValueEntry->getValue();
*/
        //$CourseId = $results[$index]['CourseId'];
        //$CourseName = $results[$index]['CourseName'];
        $CourseId = $results[$index]->course_id;
        $CourseName = $results[$index]->course_name;

        $output .= '<tr><td>'.$CourseId.'</td><td>'.$CourseName.'</td>';
        $output .= '<input type="hidden" value="'.$CourseId.'" name="_course_id">';
        $output .= '<td><input class="wp-block-button__link" type="submit" value="Update" name="edit_mode"></td>';
        $output .= '<td><input class="wp-block-button__link" type="submit" value="Delete" name="edit_mode"></td>';
        $output .= '</tr>';
    }

    $output .= '</tbody></table></figure>';

    $output .= '<div class="wp-block-buttons">';
    $output .= '<div class="wp-block-button">';
    $output .= '<input class="wp-block-button__link" type="submit" value="Create New" name="edit_mode">';
    $output .= '</div>';
    $output .= '<div class="wp-block-button">';
    $output .= '<a class="wp-block-button__link" href="/">Cancel</a>';
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</form>';

    return $output;    
}

function create_courses_table() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE `{$wpdb->prefix}courses` (
        course_id bigint(20) UNSIGNED NOT NULL,
        course_name varchar(255) NOT NULL,
        PRIMARY KEY  (course_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Delete table when deactivate
function remove_courses_table() {
    if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
    global $wpdb;
    $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}courses" );
    delete_option("my_plugin_db_version");
}    
register_deactivation_hook( __FILE__, 'remove_courses_table' );
?>