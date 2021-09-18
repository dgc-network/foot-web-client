<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('users')) {

    class users {

        /**
         * Class constructor
         */
        public function __construct() {
            add_shortcode('user_list', __CLASS__ . '::list_mode');
            add_shortcode('user_edit', __CLASS__ . '::edit_mode');
            add_shortcode('user_view', __CLASS__ . '::view_mode');
            self::create_tables();
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

        function view_mode($_id=null) {

            if ($_id==null){
                $_id=get_current_user_id();
            }

            if( isset($_POST['submit_action']) ) {
        
                global $wpdb;
                /** 
                 * submit the user relationship with course learning
                 */
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_course_learnings WHERE student_id = {$_id} ORDER BY course_id", OBJECT );
                foreach ($results as $index => $result) {
                    $table = $wpdb->prefix.'user_course_learnings';
                    $data = array(
                        'learning_date' => strtotime($_POST['_learning_date_'.$index]), 
                    );
                    $where = array(
                        'u_c_l_id' => $results[$index]->u_c_l_id
                    );
                    $wpdb->update( $table, $data, $where );
                }

                $send_address = 'DFcP5QFjbYtfgzWoqGedhxecCrRe41G3RD';
                $send_amount = 0.001;
                $send_data = 'this is my first test';
                $result = OP_RETURN_send($send_address, $send_amount, $send_data);
            
                if (isset($result['error']))
                    $result_output = 'Error: '.$result['error']."\n";
                else
                    $result_output = 'TxID: '.$result['txid']."\nWait a few seconds then check on: http://coinsecrets.org/\n";
            }
            
            /** 
             * view_mode header
             */
            $output  = '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'Name:'.'</td><td>'.get_userdata($_id)->display_name.'</td></tr>';
            $output .= '<tr><td>'.'Email:'.'</td><td>'.get_userdata($_id)->user_email.'</td></tr>';
            $output .= '</tbody></table></figure>';

            /** 
             * user relationship with course learnings
             */
            $course_header = true;
            $output .= '<figure class="wp-block-table"><table><tbody>';
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_course_learnings WHERE student_id = {$_id} ORDER BY course_id", OBJECT );
            foreach ($results as $index => $result) {
                if ($course_id == $results[$index]->course_id){$course_header=false;}
                if ($course_header) {
                    $course_id = $results[$index]->course_id;
                    $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}courses WHERE course_id = {$course_id}", OBJECT );
                    $output .= '<tr><td colspan="4">'.$row->course_title.'</td></td>';
                    $output .= '<tr><td>#</td><td>Learnings</td><td>Lecturer/Witness</td><td>Date</td></tr>';
                }

                $learningDate = wp_date( get_option( 'date_format' ), $results[$index]->learning_date );
                $output .= '<tr><td>'.$index.'</td>';
                $learning_id = $results[$index]->learning_id;
                $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE learning_id = {$learning_id}", OBJECT );
                $output .= '<td>'.$row->learning_title.'</td>';
                $output .= '<td>'.get_userdata($results[$index]->lecturer_witness_id)->display_name.'</td>';
                $output .= '<td><input type="text" name="_learning_date_'.$index.'" value="'.$learningDate.'">'.'</td>';
                $output .= '</tr>';
            }
            $output .= '</tbody></table></figure>';

            /** 
             * view_mode footer
             */
            $output .= '<div class="wp-block-buttons">';
            $output .= '<div class="wp-block-button">';
            $output .= '<input class="wp-block-button__link" type="submit" value="Submit" name="submit_action">';
            $output .= '</div>';
            $output .= '</form>';
            $output .= '<form method="get">';
            $output .= '<div class="wp-block-button">';
            $output .= '<input class="wp-block-button__link" type="submit" value="Cancel"';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</form>';

            return $output;
        }

        function edit_mode( $_id=null, $_mode ) {

            if ($_id==null){
                $_id=get_current_user_id();
                $_mode='Update';
            }

            if( isset($_POST['create_action']) ) {
        
            }
        
            if( isset($_POST['update_action']) ) {
        
            }
        
            if( isset($_POST['delete_action']) ) {

            }

            /** 
             * edit_mode
             */
            $output  = '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            if( $_mode=='Update' ) {
                $output .= '<tr><td>'.'Name:'.'</td><td><input style="width: 100%" type="text" name="_display_name" value="'.get_userdata($_id)->display_name.'"></td></tr>';
                $output .= '<tr><td>'.'Email:'.'</td><td><input style="width: 100%" type="text" name="_user_email" value="'.get_userdata($_id)->user_email.'"></td></tr>';
            } else if( $_mode=='Delete' ) {
                $output .= '<tr><td>'.'Name:'.'</td><td><input style="width: 100%" type="text" name="_display_name" value="'.get_userdata($_id)->display_name.'" disabled></td></tr>';
                $output .= '<tr><td>'.'Email:'.'</td><td><input style="width: 100%" type="text" name="_user_email" value="'.get_userdata($_id)->user_email.'" disabled></td></tr>';
            } else {
                $output .= '<tr><td>'.'Name:'.'</td><td><input style="width: 100%" type="text" name="_display_name" value=""></td></tr>';
                $output .= '<tr><td>'.'Email:'.'</td><td><input style="width: 100%" type="text" name="_user_email" value=""></td></tr>';
            }
            $output .= '</tbody></table></figure>';
    
            $output .= '<div class="wp-block-buttons">';
            $output .= '<div class="wp-block-button">';
            if( $_mode=='Update' ) {
                //$output .= '<input class="wp-block-button__link" type="submit" value="Update" name="update_action">';
            } else if( $_mode=='Delete' ) {
                //$output .= '<input class="wp-block-button__link" type="submit" value="Delete" name="delete_action">';
            } else {
                //$output .= '<input class="wp-block-button__link" type="submit" value="Create" name="create_action">';
            }
            $output .= '</div>';
            $output .= '<div class="wp-block-button">';
            $output .= '<input class="wp-block-button__link" type="submit" value="Cancel"';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</form>';
        
            return $output;
        }

        function list_mode() {

            if( isset($_GET['view_mode']) ) {
                return self::view_mode($_GET['_id']);
            }

            if( isset($_POST['edit_mode']) ) {
                return self::edit_mode($_POST['_id'], $_POST['edit_mode']);
            }            

            /**
             * List Mode
             */                    
            $output  = '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>Name</td><td>Email</td><td>--</td><td>--</td></tr>';
        
            $results = get_users();
            foreach ($results as $index => $result) {

                $userId = $results[$index]->ID;
                $userTitle = $results[$index]->display_name;
                $userEmail = $results[$index]->user_email;

                $output .= '<form method="post">';
                $output .= '<tr>';
                $output .= '<td>'.$userTitle.'</td>';
                $output .= '<td><a href="?view_mode=true&_id='.$userId.'">'.$userEmail.'</a></td>';
                $output .= '<input type="hidden" value="'.$userId.'" name="_id">';
                $output .= '<td><input class="wp-block-button__link" type="submit" value="Update" name="edit_mode"></td>';
                $output .= '<td><input class="wp-block-button__link" type="submit" value="Delete" name="edit_mode"></td>';
                $output .= '</tr>';
                $output .= '</form>';
            }        
            $output .= '</tbody></table></figure>';
        
            $output .= '<form method="post">';
            $output .= '<div class="wp-block-buttons">';
            $output .= '<div class="wp-block-button">';
            $output .= '<input class="wp-block-button__link" type="submit" value="Create" name="edit_mode">';
            $output .= '</div>';
            $output .= '<div class="wp-block-button">';
            $output .= '<a class="wp-block-button__link" href="/">Cancel</a>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</form>';
        
            return $output;    
        }
        
        function select_options( $default_id=null ) {

            $results = get_users();
            $output = '<option value="no_select">-- Select an option --</option>';
            foreach ($results as $index => $result) {
                if ( $results[$index]->ID == $default_id ) {
                    $output .= '<option value="'.$results[$index]->ID.'" selected>';
                } else {
                    $output .= '<option value="'.$results[$index]->ID.'">';
                }
                $output .= $results[$index]->display_name;
                $output .= '</option>';        
            }
            $output .= '<option value="delete_select">-- Remove this --</option>';
            return $output;    
        }

        function create_tables() {
        
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $sql = "CREATE TABLE `{$wpdb->prefix}user_course_learnings` (
                u_c_l_id int NOT NULL AUTO_INCREMENT,
                student_id int NOT NULL,
                course_id int,
                learning_id int,
                learning_date int,
                lecturer_witness_id int,
                PRIMARY KEY  (u_c_l_id)
            ) $charset_collate;";        
            dbDelta($sql);
        }
        
    }
    //if ( is_admin() )
    new users();
}
?>