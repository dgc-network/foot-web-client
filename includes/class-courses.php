<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('courses')) {

    class courses {

        /**
         * Class constructor
         */
        public function __construct() {
            add_shortcode('course_shortcode', __CLASS__ . '::shortcode_callback');
            self::create_table();
        }


        function shortcode_callback() {

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
                $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}courses WHERE course_id = {$_POST['_id']}", OBJECT );
                $CreateDate = wp_date( get_option( 'date_format' ), $row->create_date );
                if( $_POST['edit_mode']=='Create New' ) {
                    $row=array();
                }
                $output  = '<form method="post">';
                $output .= '<figure class="wp-block-table"><table><tbody>';
                if( $_POST['edit_mode']=='Create New' ) {
                    $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_course_title" value="'.$row->course_title.'"></td></tr>';
                }
                if( $_POST['edit_mode']=='Update' ) {
                    $output .= '<tr><td>'.'ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_course_id" value="'.$row->course_id.'" disabled></td></tr>';
                    $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_course_title" value="'.$row->course_title.'"></td></tr>';
                    $output .= '<tr><td>'.'Created:'.'</td><td><input style="width: 100%" type="date" name="_create_date" value="'.$CreateDate.'" disabled></td></tr>';
                }
                if( $_POST['edit_mode']=='Delete' ) {
                    $output .= '<tr><td>'.'ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_course_id" value="'.$row->course_id.'" disabled></td></tr>';
                    $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_course_title" value="'.$row->course_title.'" disabled></td></tr>';
                    $output .= '<tr><td>'.'Created:'.'</td><td><input style="width: 100%" type="date" name="_create_date" value="'.$CreateDate.'" disabled></td></tr>';
                }
                $output .= '</tbody></table></figure>';
        
                $output .= '<div class="wp-block-buttons">';
                $output .= '<div class="wp-block-button">';
                if( $_POST['edit_mode']=='Create New' ) {
                    $output .= '<input class="wp-block-button__link" type="submit" value="Create" name="create_action">';
                }
                if( $_POST['edit_mode']=='Update' ) {
                    $output .= '<input class="wp-block-button__link" type="submit" value="Update" name="update_action">';
                }
                if( $_POST['edit_mode']=='Delete' ) {
                    $output .= '<input class="wp-block-button__link" type="submit" value="Delete" name="delete_action">';
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
        
                global $wpdb;
                $table = $wpdb->prefix.'courses';
                $data = array(
                    'create_date' => current_time('timestamp'), 
                    'course_title' => $_POST['_course_title']
                );
                $format = array('%d', '%s');
                $wpdb->insert($table, $data, $format);
/*                
                $my_id = $wpdb->insert_id;
        
                $Roles = array();
                $KeyValueEntries = array();
        
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
        
                //get_post_timestamp();
                global $wpdb;
                $table = $wpdb->prefix.'courses';
                $data = array(
                    'course_title' => $_POST['_course_title']
                    //'course_date' => get_post_timestamp($_POST['_course_date'])
                );
                $where = array('course_id' => $_POST['_course_id']);
                //$format = array('%d', '%s');
                $updated = $wpdb->update( $table, $data, $where );
/*         
                if ( false === $updated ) {
                    // There was an error.
                } else {
                    // No error. You can check updated to see how many rows were changed.
                }
                
                $Roles = array();
                $KeyValueEntries = array();
        
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
        
            if( isset($_POST['delete_action']) ) {
        
                global $wpdb;
                $table = $wpdb->prefix.'courses';
                $where = array('course_id' => $_POST['_course_id']);
                //$format = array('%d', '%s');
                $deleted = $wpdb->delete( $table, $where );
            }

            /**
             * List Mode
             */                    
            $output  = '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>Title</td><td>Date</td><td>--</td><td>--</td></tr>';
        
            //$metadata = '';
            //$agents = $AgentList->getAgents();
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}courses", OBJECT );
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
                $CourseTitle = $results[$index]->course_title;
                //$CourseDate = $results[$index]->course_date;
                $CreateDate = wp_date( get_option( 'date_format' ), $results[$index]->create_date );
        
                $output .= '<form method="post" name="form_'.$index.'">';
                $output .= '<tr>';
                $output .= '<td>'.$CourseTitle.'</td>';
                $output .= '<td>'.$CreateDate.'</td>';
                $output .= '<input type="hidden" value="'.$CourseId.'" name="_id">';
                $output .= '<td><input class="wp-block-button__link" type="submit" value="Update" name="edit_mode"></td>';
                $output .= '<td><input class="wp-block-button__link" type="submit" value="Delete" name="edit_mode"></td>';
                $output .= '</tr>';
                $output .= '</form>';
            }
        
            $output .= '</tbody></table></figure>';
        
            $output .= '<form method="post">';
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
        
        function create_table() {
        
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
            $sql = "CREATE TABLE `{$wpdb->prefix}courses` (
                course_id int NOT NULL AUTO_INCREMENT,
                course_title varchar(255) NOT NULL,
                create_date int NOT NULL,
                PRIMARY KEY  (course_id)
            ) $charset_collate;";        
            dbDelta($sql);
        }
        
        // Delete table when deactivate
        function remove_table() {
            if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
            global $wpdb;
            $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}courses" );
            delete_option("my_plugin_db_version");
        } 

    }
    new courses();
}
?>