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
            self::create_tables();
        }

        function shortcode_callback() {

            if( isset($_POST['submit_action']) ) {
                //return $_POST['submit_action'];
        
                global $wpdb;
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_lecturers WHERE course_id = {$_GET['_id']}", OBJECT );
                foreach ($results as $index => $result) {
                    $table = $wpdb->prefix.'course_lecturers';
                    $data = array(
                        'expired_date' => strtotime($_POST['_expired_date_'].$index),
                        'lecturer_id' => $_POST['_lecturer_id_'.$index]
                    );
                    $where = array(
                        'c_l_id' => $results[$index]->c_l_id
                    );
                    $updated = $wpdb->update( $table, $data, $where );
                }
                if (( $_POST['_lecturer_id']=='no_select' ) || ( $_POST['_lecturer_id']=='delete_select' ) ){
                } else {
                    $table = $wpdb->prefix.'course_lecturers';
                    $data = array(
                        'expired_date' => strtotime($_POST['_expired_date']), 
                        'lecturer_id' => $_POST['_lecturer_id'],
                        'course_id' => $_GET['_id']
                    );
                    $format = array('%d', '%d', '%d');
                    $wpdb->insert($table, $data, $format);
                }
            }
            
            if( isset($_GET['view_mode']) ) {
                global $wpdb;
                $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}courses WHERE course_id = {$_GET['_id']}", OBJECT );
                $CreateDate = wp_date( get_option( 'date_format' ), $row->create_date );
                $output  = '<form method="post">';
                $output .= '<figure class="wp-block-table"><table><tbody>';
                $output .= '<tr><td>'.'Title:'.'</td><td>'.$row->course_title.'</td></tr>';
                $output .= '<tr><td>'.'Created:'.'</td><td>'.$CreateDate.'</td></tr>';
                $output .= '</tbody></table></figure>';

                $output .= '<figure class="wp-block-table"><table><tbody>';
                $output .= '<tr><td>'.'#'.'</td><td>'.'Lecturers'.'</td><td>Expired</td></tr>';
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_lecturers WHERE course_id = {$_GET['_id']}", OBJECT );
                foreach ($results as $index => $result) {
                    $output .= '<tr><td>'.$index.'</td>';
                    $output .= '<td>'.'<select name="_lecturer_id_'.$index.'">'.Users::select_options($results[$index]->lecturer_id).'</td>';
                    $ExpireDate = wp_date( get_option( 'date_format' ), $results[$index]->expired_date );
                    $output .= '<td><input type="text" name="_expired_date_'.$index.'" value="'.$ExpireDate.'">'.'</td></tr>';
                }
                $output .= '<tr><td>'.($index+1).'</td>';
                $output .= '<td>'.'<select name="_lecturer_id">'.Users::select_options().'</select>'.'</td>';
                $output .= '<td><input type="date" name="_expired_date"></td></tr>';
                $output .= '</tbody></table></figure>';
                
                $output .= '<div class="wp-block-buttons">';
                $output .= '<div class="wp-block-button">';
                //$output .= '<input type="hidden" value="'.$_GET['_id'].'" name="_course_id">';
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
                    $output .= '<tr><td>'.'ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_course_id" value="'.$row->course_id.'"></td></tr>';
                    $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_course_title" value="'.$row->course_title.'"></td></tr>';
                    $output .= '<tr><td>'.'Created:'.'</td><td><input style="width: 100%" type="text" name="_create_date" value="'.$CreateDate.'" disabled></td></tr>';
                }
                if( $_POST['edit_mode']=='Delete' ) {
                    $output .= '<tr><td>'.'ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_course_id" value="'.$row->course_id.'"></td></tr>';
                    $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_course_title" value="'.$row->course_title.'" disabled></td></tr>';
                    $output .= '<tr><td>'.'Created:'.'</td><td><input style="width: 100%" type="text" name="_create_date" value="'.$CreateDate.'" disabled></td></tr>';
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
            }
        
            if( isset($_POST['update_action']) ) {
        
                global $wpdb;
                $table = $wpdb->prefix.'courses';
                $data = array(
                    'course_title' => $_POST['_course_title']
                );
                $where = array('course_id' => $_POST['_course_id']);
                $wpdb->update( $table, $data, $where );
            }
        
            if( isset($_POST['delete_action']) ) {
        
                global $wpdb;
                $table = $wpdb->prefix.'courses';
                $where = array('course_id' => $_POST['_course_id']);
                $deleted = $wpdb->delete( $table, $where );
            }

            /**
             * List Mode
             */                    
            $output  = '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>Title</td><td>Created</td><td>--</td><td>--</td></tr>';
        
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}courses", OBJECT );
            foreach ($results as $index => $result) {

                $CourseId = $results[$index]->course_id;
                $CourseTitle = $results[$index]->course_title;
                $CreateDate = wp_date( get_option( 'date_format' ), $results[$index]->create_date );
        
                $output .= '<form method="post">';
                $output .= '<tr>';
                $output .= '<td><a href="?view_mode=true&_id='.$CourseId.'">'.$CourseTitle.'</a></td>';
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
        
        function select_options( $default_id=null ) {

            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}courses", OBJECT );
            $output = '<option value="no_select">-- Select an option --</option>';
            foreach ($results as $index => $result) {
                if ( $results[$index]->course_id == $default_id ) {
                    $output .= '<option value="'.$results[$index]->course_id.'" selected>';
                } else {
                    $output .= '<option value="'.$results[$index]->course_id.'">';
                }
                $output .= $results[$index]->course_title;
                $output .= '</option>';        
            }
            $output .= '<option value="delete_select">-- Remove this Select --</option>';
            return $output;    
        }


        function create_tables() {
        
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

            $sql = "CREATE TABLE `{$wpdb->prefix}course_lecturers` (
                c_l_id int NOT NULL AUTO_INCREMENT,
                course_id int NOT NULL,
                lecturer_id int NOT NULL,
                expired_date int NOT NULL,
                PRIMARY KEY  (c_l_id)
            ) $charset_collate;";        
            dbDelta($sql);
        }
        
        // Delete table when deactivate
        function remove_tables() {
            if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
            global $wpdb;
            $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}courses" );
            delete_option("my_plugin_db_version");
        } 

    }
    new courses();
}
?>