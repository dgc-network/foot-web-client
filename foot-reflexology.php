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
        $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}courses WHERE course_id = 1", OBJECT );
        $output  = '<form method="post">';
        $output .= '<figure class="wp-block-table"><table><tbody>';
        $output .= '<tr><td>'.'CourseId:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_CourseId" value="'.$row->CourseId.'"></td></tr>';
        $output .= '<tr><td>'.'CourseName:'.'</td><td><input style="width: 100%" type="text" name="_CourseName" value="'.$_POST['_item'].'"></td></tr>';
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
                ( course_id, course_name, meta_value )
                VALUES ( %d, %s, %s )
                ",
                array(
                    10,
                    $metakey,
                    $metavalue,
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
    $output .= '<tr><td>CourseId</td><td>CourseName</td><td></td><td></td></tr>';

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
        $CourseId = $results[$index]['CourseId'];
        $CourseName = $results[$index]['CourseName'];

        $output .= '<tr><td>'.$CourseId.'</td><td>'.$CourseName.'</td>';
        $output .= '<input type="hidden" value="item_'.$index.'" name="_item">';
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

?>