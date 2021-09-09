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
            add_shortcode('user_shortcode', __CLASS__ . '::shortcode_callback');
            self::create_tables();
        }


        function shortcode_callback() {

            if( isset($_POST['submit_action']) ) {
                //return $_POST['submit_action'];
        
                global $wpdb;
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_courses WHERE user_id = {$_GET['_id']}", OBJECT );
                foreach ($results as $index => $result) {
                    $table = $wpdb->prefix.'user_courses';
                    $data = array(
                        //'user_date' => strtotime($_POST['_user_date']),
                        'course_id' => $_POST['_course_id_'.$index]
                    );
                    $where = array(
                        't_c_id' => $results[$index]->t_c_id
                    );
                    $updated = $wpdb->update( $table, $data, $where );
                }
                if (( $_POST['_course_id']=='no_select' ) || ( $_POST['_course_id']=='delete_select' ) ){
                } else {
                    $table = $wpdb->prefix.'user_courses';
                    $data = array(
                        //'create_date' => strtotime($_POST['_create_date']), 
                        'user_id' => $_POST['_user_id'],
                        'course_id' => $_POST['_course_id']
                    );
                    $format = array('%d', '%d');
                    $wpdb->insert($table, $data, $format);    
                }
            }
            
            if( isset($_GET['view_mode']) ) {
                global $wpdb;
                $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}users WHERE user_id = {$_GET['_id']}", OBJECT );
                $userDate = wp_date( get_option( 'date_format' ), $row->user_date );
                $output  = '<form method="post">';
                $output .= '<figure class="wp-block-table"><table><tbody>';
                $output .= '<tr><td>'.'Title:'.'</td><td>'.$row->user_title.'</td></tr>';
                $output .= '<tr><td>'.'Date:'.'</td><td>'.$userDate.'</td></tr>';
                $output .= '</tbody></table></figure>';

                $output .= '<figure class="wp-block-table"><table><tbody>';
                $output .= '<tr><td>'.'#'.'</td><td>'.'Courses'.'</td></tr>';
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_courses WHERE user_id = {$_GET['_id']}", OBJECT );
                foreach ($results as $index => $result) {
                    $output .= '<tr><td>'.$index.'</td><td>'.'<select name="_course_id_'.$index.'">'.Courses::select_options($results[$index]->course_id).'</td></tr>';
                    //$output .= '<input type="hidden" value="'.$index.'" name="_index">';
                }
                $output .= '<tr><td>'.($index+1).'</td><td>'.'<select name="_course_id">'.Courses::select_options().'</select>'.'</td></tr>';
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
                $output .= '<input type="hidden" value="'.$_GET['_id'].'" name="_user_id">';
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
            
            if( isset($_POST['edit_mode']) ) {
        
            //$AgentList = new AgentList();
            //$Agent = new Agent();
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
                $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}users WHERE user_id = {$_POST['_id']}", OBJECT );
                $userDate = wp_date( get_option( 'date_format' ), $row->user_date );
                if( $_POST['edit_mode']=='Create New' ) {
                    $row=array();
                }
                //$userDate = wp_date( get_option( 'date_format' ), get_post_timestamp() );
                $output  = '<form method="post">';
                $output .= '<figure class="wp-block-table"><table><tbody>';
                if( $_POST['edit_mode']=='Create New' ) {
                    $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_user_title" value="'.$row->user_title.'"></td></tr>';
                    $output .= '<tr><td>'.'Date:'.'</td><td><input style="width: 100%" type="date" name="_user_date" value="'.$userDate.'"></td></tr>';
                }
                if( $_POST['edit_mode']=='Update' ) {
                    $output .= '<tr><td>'.'ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_user_id" value="'.$row->user_id.'"></td></tr>';
                    $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_user_title" value="'.$row->user_title.'"></td></tr>';
                    $output .= '<tr><td>'.'Date:'.'</td><td><input style="width: 100%" type="date" name="_user_date" value="'.$userDate.'"></td></tr>';
                }
                if( $_POST['edit_mode']=='Delete' ) {
                    $output .= '<tr><td>'.'ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_user_id" value="'.$row->user_id.'"></td></tr>';
                    $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_user_title" value="'.$row->user_title.'" disabled></td></tr>';
                    $output .= '<tr><td>'.'Date:'.'</td><td><input style="width: 100%" type="date" name="_user_date" value="'.$userDate.'" disabled></td></tr>';
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
                $table = $wpdb->prefix.'users';
                $data = array(
                    'user_date' => strtotime($_POST['_user_date']), 
                    'user_title' => $_POST['_user_title']
                );
                $format = array('%d', '%s');
                $wpdb->insert($table, $data, $format);
                $my_id = $wpdb->insert_id;
        
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
        
                global $wpdb;
                $table = $wpdb->prefix.'users';
                $data = array(
                    'user_title' => $_POST['_user_title'],
                    'user_date' => strtotime($_POST['_user_date'])
                );
                $where = array('user_id' => $_POST['_user_id']);
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
                $table = $wpdb->prefix.'users';
                $where = array('user_id' => $_POST['_user_id']);
                $deleted = $wpdb->delete( $table, $where );
            }

            /**
             * List Mode
             */                    
            $output  = '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>Name</td><td>Email</td><td>--</td><td>--</td></tr>';
        
            //$agents = $AgentList->getAgents();
            //global $wpdb;
            //$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}users", OBJECT );
            $results = get_users();
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
                $userId = $results[$index]->user_id;
                $userTitle = $results[$index]->display_name;
                $userEmail = $results[$index]->user_email;
                //$userDate = $results[$index]->user_date;
                //$userDate = wp_date( get_option( 'date_format' ), $results[$index]->user_date );
        
                $output .= '<form method="post">';
                $output .= '<tr>';
                $output .= '<td><a href="?view_mode=true&_id='.$userId.'">'.$userTitle.'</a></td>';
                $output .= '<td>'.$userEmail.'</td>';
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
            $output .= '<input class="wp-block-button__link" type="submit" value="Create New" name="edit_mode">';
            $output .= '</div>';
            $output .= '<div class="wp-block-button">';
            $output .= '<a class="wp-block-button__link" href="/">Cancel</a>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</form>';
        
            return $output;    
        }
        
        function create_tables() {
        
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
            $sql = "CREATE TABLE `{$wpdb->prefix}users` (
                user_id int NOT NULL AUTO_INCREMENT,
                user_title varchar(255) NOT NULL,
                user_date int NOT NULL,
                PRIMARY KEY  (user_id)
            ) $charset_collate;";        
            dbDelta($sql);

            $sql = "CREATE TABLE `{$wpdb->prefix}user_courses` (
                t_c_id int NOT NULL AUTO_INCREMENT,
                user_id int NOT NULL,
                course_id int NOT NULL,
                PRIMARY KEY  (t_c_id)
            ) $charset_collate;";        
            dbDelta($sql);
        }
        
        // Delete table when deactivate
        function remove_table() {
            if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
            global $wpdb;
            $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}users" );
            delete_option("my_plugin_db_version");

        }        

    }
    new users();
}
?>