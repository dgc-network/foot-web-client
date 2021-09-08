<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('teaches')) {

    class teaches {

        /**
         * Class constructor
         */
        public function __construct() {
            add_shortcode('teach_shortcode', __CLASS__ . '::shortcode_callback');
            self::create_table();
        }


        function shortcode_callback() {

            //remove_courses_table();
            //create_courses_table();
        
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
                $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}teaches WHERE teach_id = {$_POST['_id']}", OBJECT );
                if( $_POST['edit_mode']=='Create New' ) {
                    $row=array();
                }
                //$TeachDate = wp_date( get_option( 'date_format' ), get_post_timestamp() );
                $TeachDate = wp_date( get_option( 'date_format' ), $row->teach_date );
                $output  = '<form method="post">';
                $output .= '<figure class="wp-block-table"><table><tbody>';
                $output .= '<tr><td>'.'ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_teach_id" value="'.$row->teach_id.'"></td></tr>';
                $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_teach_title" value="'.$row->teach_title.'"></td></tr>';
                $output .= '<tr><td>'.'Date:'.'</td><td><input style="width: 100%" type="date" name="_teach_date" value="'.$TeachDate.'"></td></tr>';
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
                $table = $wpdb->prefix.'teaches';
                $data = array('teach_id' => $_POST['_teach_id'], 'teach_title' => $_POST['_teach_title']);
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
                $table = $wpdb->prefix.'teaches';
                $data = array('teach_title' => $_POST['_teach_title']);
                $where = array('teach_id' => $_POST['_teach_id']);
                //$format = array('%d', '%s');
                $updated = $wpdb->update( $table, $data, $where );
         
                if ( false === $updated ) {
                    // There was an error.
                } else {
                    // No error. You can check updated to see how many rows were changed.
                }
                
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
        
            if( isset($_POST['delete_action']) ) {
        
                global $wpdb;
                $table = $wpdb->prefix.'teaches';
                //$data = array('course_name' => $_POST['_course_name']);
                $where = array('teach_id' => $_POST['_teach_id']);
                //$format = array('%d', '%s');
                $deleted = $wpdb->delete( $table, $where );
            }

            /**
             * List Mode
             */        
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}teaches", OBJECT );
            
            $output  = '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>Title</td><td>Date</td><td>--</td><td>--</td></tr>';
        
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
                $TeachId = $results[$index]->teach_id;
                $TeachTitle = $results[$index]->teach_title;
                $TeachDate = $results[$index]->teach_date;
        
                $output .= '<tr>';
                $output .= '<td>'.$TeachTitle.'</td>';
                $output .= '<td>'.$TeachDate.'</td>';
                $output .= '<input type="hidden" value="'.$TeachId.'" name="_id">';
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
        
        function create_table() {
        
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
        
            $sql = "CREATE TABLE `{$wpdb->prefix}teaches` (
                teach_id bigint(20) UNSIGNED NOT NULL,
                teach_title varchar(255) NOT NULL,
                teach_date bigint(20) UNSIGNED NOT NULL,
                PRIMARY KEY  (teach_id)
            ) $charset_collate;";
        
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
        
        // Delete table when deactivate
        function remove_table() {
            if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
            global $wpdb;
            $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}teaches" );
            delete_option("my_plugin_db_version");
        } 





        /**
         * Shortcode Wrapper.
         *
         * @param string[] $function Callback function.
         * @param array    $atts     Attributes. Default to empty array.
         *
         * @return string
         */
        public static function shortcode_wrapper($function, $atts = array()) {
            ob_start();
            call_user_func($function, $atts);
            return ob_get_clean();
        }

        /**
         * Shortcode callback
         * @param array $atts
         * @return string
         */
        public static function teaches_shortcode_callback($atts) {
            return self::shortcode_wrapper(array('teaches', 'teaches_shortcode_output'), $atts);
        }

        /**
         * Shortcode output
         * @param array $atts
         */
        public static function teaches_shortcode_output($atts) {

            echo $atts.' is here!';
/*            
            if (!is_user_logged_in()) {
                echo '<div class="woocommerce">';
                wc_get_template('myaccount/form-login.php');
                echo '</div>';
            } else {
                wp_enqueue_style('dashicons');
                wp_enqueue_style('select2');
                wp_enqueue_style('jquery-datatables-style');
                wp_enqueue_style('jquery-datatables-responsive-style');
                wp_enqueue_script('jquery-datatables-script');
                wp_enqueue_script('jquery-datatables-responsive-script');
                wp_enqueue_script('selectWoo');
                wp_enqueue_script('dgc-wallet-endpoint');
                if (isset($_GET['payment_action']) && !empty($_GET['payment_action'])) {
                    if ('view_transactions' === $_GET['payment_action']) {
                        dgc_wallet()->get_template('dgc-wallet-endpoint-transactions.php');
                    } else if (in_array($_GET['payment_action'], apply_filters('dgc_wallet_endpoint_actions', array('add', 'transfer')))) {
                        dgc_wallet()->get_template('dgc-wallet-endpoint.php');
                    }
                    do_action('dgc_wallet_shortcode_action', $_GET['payment_action']);
                } else {
                    dgc_wallet()->get_template('dgc-wallet-endpoint.php');
                }
            }
*/            
        }        

    }
    new teaches();
}
?>