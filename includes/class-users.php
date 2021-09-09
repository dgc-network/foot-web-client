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
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_courses WHERE student_id = {$_GET['_id']}", OBJECT );
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
                        'student_id' => $_GET['_id'],
                        'course_id' => $_POST['_course_id']
                    );
                    $format = array('%d', '%d');
                    $wpdb->insert($table, $data, $format);    
                }
            }
            
            if( isset($_GET['view_mode']) ) {
                //return $_GET['view_mode'];

                $output  = '<form method="post">';
                $output .= '<figure class="wp-block-table"><table><tbody>';
                $output .= '<tr><td>'.'Name:'.'</td><td>'.get_userdata($_GET['_id'])->display_name.'</td></tr>';
                $output .= '<tr><td>'.'Email:'.'</td><td>'.get_userdata($_GET['_id'])->user_email.'</td></tr>';
                $output .= '</tbody></table></figure>';
                return $output;

                $output .= '<figure class="wp-block-table"><table><tbody>';
                $output .= '<tr><td>'.'#'.'</td><td>'.'Courses'.'</td></tr>';
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_courses WHERE student_id = {$_GET['_id']}", OBJECT );
                foreach ($results as $index => $result) {
                    $output .= '<tr><td>'.$index.'</td><td>'.'<select name="_course_id_'.$index.'">'.Courses::select_options($results[$index]->course_id).'</td></tr>';
                }
                $output .= '<tr><td>'.($index+1).'</td><td>'.'<select name="_course_id">'.Courses::select_options().'</select>'.'</td></tr>';
                $output .= '</tbody></table></figure>';
                
                $output .= '<div class="wp-block-buttons">';
                $output .= '<div class="wp-block-button">';
                //$output .= '<input type="hidden" value="'.$_GET['_id'].'" name="_student_id">';
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
        
                $output  = '<form method="post">';
                $output .= '<figure class="wp-block-table"><table><tbody>';
                if( $_POST['edit_mode']=='Create New' ) {
                    $output .= '<tr><td>'.'Name:'.'</td><td><input style="width: 100%" type="text" name="_display_name" value=""></td></tr>';
                    $output .= '<tr><td>'.'Email:'.'</td><td><input style="width: 100%" type="text" name="_user_email" value=""></td></tr>';
                }
                if( $_POST['edit_mode']=='Update' ) {
                    $output .= '<tr><td>'.'Name:'.'</td><td><input style="width: 100%" type="text" name="_display_name" value="'.get_userdata($_POST['_id'])->display_name.'"></td></tr>';
                    $output .= '<tr><td>'.'Email:'.'</td><td><input style="width: 100%" type="text" name="_user_email" value="'.get_userdata($_POST['_id'])->user_email.'"></td></tr>';
                }
                if( $_POST['edit_mode']=='Delete' ) {
                    $output .= '<tr><td>'.'Name:'.'</td><td><input style="width: 100%" type="text" name="_display_name" value="'.get_userdata($_POST['_id'])->display_name.'" disabled></td></tr>';
                    $output .= '<tr><td>'.'Email:'.'</td><td><input style="width: 100%" type="text" name="_user_email" value="'.get_userdata($_POST['_id'])->user_email.'" disabled></td></tr>';
                }
                $output .= '</tbody></table></figure>';
        
                $output .= '<div class="wp-block-buttons">';
                $output .= '<div class="wp-block-button">';
                if( $_POST['edit_mode']=='Create New' ) {
                    //$output .= '<input class="wp-block-button__link" type="submit" value="Create" name="create_action">';
                }
                if( $_POST['edit_mode']=='Update' ) {
                    //$output .= '<input class="wp-block-button__link" type="submit" value="Update" name="update_action">';
                }
                if( $_POST['edit_mode']=='Delete' ) {
                    //$output .= '<input class="wp-block-button__link" type="submit" value="Delete" name="delete_action">';
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
        
            }
        
            if( isset($_POST['update_action']) ) {
        
            }
        
            if( isset($_POST['delete_action']) ) {

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
            $output .= '<input class="wp-block-button__link" type="submit" value="Create New" name="edit_mode">';
            $output .= '</div>';
            $output .= '<div class="wp-block-button">';
            $output .= '<a class="wp-block-button__link" href="/">Cancel</a>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</form>';
        
            return $output;    
        }
        
        function select_options( $default_id=null ) {

            //global $wpdb;
            //$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}courses", OBJECT );
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
            $output .= '<option value="delete_select">-- Remove this Select --</option>';
            return $output;    
        }

        function create_tables() {
        
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
            $sql = "CREATE TABLE `{$wpdb->prefix}user_courses` (
                u_c_id int NOT NULL AUTO_INCREMENT,
                student_id int NOT NULL,
                course_id int NOT NULL,
                lecturer_id int,
                witness_id int,
                certification_date int,
                PRIMARY KEY  (u_c_id)
            ) $charset_collate;";        
            dbDelta($sql);
        }
        
        // Delete table when deactivate
        function remove_tables() {
            if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
            global $wpdb;
            $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}user_courses" );
            delete_option("my_plugin_db_version");

        }        

    }
    new users();
}
?>