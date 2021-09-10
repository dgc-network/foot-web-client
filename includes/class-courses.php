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
            add_shortcode('course_list', __CLASS__ . '::list_mode');
            add_shortcode('course_edit', __CLASS__ . '::edit_mode');
            add_shortcode('course_view', __CLASS__ . '::view_mode');
            self::create_tables();
        }

        function edit_mode( $_id=null, $_mode ) {

            if ($_id==null){
                $_mode='Create';
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
                return;
            }
        
            if( isset($_POST['update_action']) ) {
        
                global $wpdb;
                $table = $wpdb->prefix.'courses';
                $data = array(
                    'course_title' => $_POST['_course_title']
                );
                $where = array('course_id' => $_POST['_course_id']);
                $wpdb->update( $table, $data, $where );
                //unset($_GET['_id']);
                //unset($_GET['edit_mode']);
                //return;
                wp_redirect(home_url());
                exit;
            }
        
            if( isset($_POST['delete_action']) ) {
        
                global $wpdb;
                $table = $wpdb->prefix.'courses';
                $where = array('course_id' => $_POST['_course_id']);
                $deleted = $wpdb->delete( $table, $where );
                wp_redirect(home_url());
                exit;
            }

            /** 
             * edit_mode
             */
            global $wpdb;
            $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}courses WHERE course_id = {$_id}", OBJECT );
            $CreateDate = wp_date( get_option( 'date_format' ), $row->create_date );
            $output  = '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            if( $_mode=='Update' ) {
                $output .= '<tr><td>'.'ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_course_id" value="'.$row->course_id.'"></td></tr>';
                $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_course_title" value="'.$row->course_title.'"></td></tr>';
                $output .= '<tr><td>'.'Created:'.'</td><td><input style="width: 100%" type="text" name="_create_date" value="'.$CreateDate.'" disabled></td></tr>';
            } else if( $_mode=='Delete' ) {
                $output .= '<tr><td>'.'ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_course_id" value="'.$row->course_id.'"></td></tr>';
                $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_course_title" value="'.$row->course_title.'" disabled></td></tr>';
                $output .= '<tr><td>'.'Created:'.'</td><td><input style="width: 100%" type="text" name="_create_date" value="'.$CreateDate.'" disabled></td></tr>';
            } else if( $_mode=='Create' ){
                $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_course_title" value=""></td></tr>';
            }
            $output .= '</tbody></table></figure>';
    
            $output .= '<div class="wp-block-buttons">';
            $output .= '<div class="wp-block-button">';
            if( $_mode=='Update' ) {
                $output .= '<input class="wp-block-button__link" type="submit" value="Update" name="update_action">';
            } else if( $_mode=='Delete' ) {
                $output .= '<input class="wp-block-button__link" type="submit" value="Delete" name="delete_action">';
            } else if( $_mode=='Create' ){
                $output .= '<input class="wp-block-button__link" type="submit" value="Create" name="create_action">';
            }
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

        function view_mode( $_id=null ) {

            if ($_id==null){
                return '<div>ID is required</div>';
            }

            if( isset($_POST['submit_action']) ) {
        
                global $wpdb;
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_lecturers WHERE course_id = {$_id}", OBJECT );
                foreach ($results as $index => $result) {
                    if ( $_POST['_lecturer_id_'.$index]=='delete_select' ){
                        $table = $wpdb->prefix.'course_lecturers';
                        $where = array(
                            'c_l_id' => $results[$index]->c_l_id
                        );
                        $wpdb->delete( $table, $where );    
                    } else {
                        $table = $wpdb->prefix.'course_lecturers';
                        $data = array(
                            'expired_date' => strtotime($_POST['_expired_date_'.$index]),
                            'lecturer_id' => $_POST['_lecturer_id_'.$index]
                        );
                        $where = array(
                            'c_l_id' => $results[$index]->c_l_id
                        );
                        $wpdb->update( $table, $data, $where );    
                    }
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

                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_witnesses WHERE course_id = {$_id}", OBJECT );
                foreach ($results as $index => $result) {
                    if ( $_POST['_witness_id_'.$index]=='delete_select' ){
                        $table = $wpdb->prefix.'course_witnesses';
                        $where = array(
                            'c_w_id' => $results[$index]->c_w_id
                        );
                        $wpdb->delete( $table, $where );    
                    } else {
                        $table = $wpdb->prefix.'course_witnesses';
                        $data = array(
                            'expired_date' => strtotime($_POST['_w_expired_date_'.$index]),
                            'witness_id' => $_POST['_witness_id_'.$index]
                        );
                        $where = array(
                            'c_w_id' => $results[$index]->c_w_id
                        );
                        $wpdb->update( $table, $data, $where );
                    }
                }
                if (( $_POST['_witness_id']=='no_select' ) || ( $_POST['_witness_id']=='delete_select' ) ){
                } else {
                    $table = $wpdb->prefix.'course_witnesses';
                    $data = array(
                        'expired_date' => strtotime($_POST['_w_expired_date']), 
                        'witness_id' => $_POST['_witness_id'],
                        'course_id' => $_GET['_id']
                    );
                    $format = array('%d', '%d', '%d');
                    $wpdb->insert($table, $data, $format);
                }
            }

            /** 
             * view_mode
             */
            global $wpdb;
            $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}courses WHERE course_id = {$_id}", OBJECT );
            $CreateDate = wp_date( get_option( 'date_format' ), $row->create_date );
            $output  = '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'Title:'.'</td><td>'.$row->course_title.'</td></tr>';
            $output .= '<tr><td>'.'Created:'.'</td><td>'.$CreateDate.'</td></tr>';
            $output .= '</tbody></table></figure>';

            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'#'.'</td><td>'.'Lecturers'.'</td><td>Expired Date</td></tr>';
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_lecturers WHERE course_id = {$_id}", OBJECT );
            foreach ($results as $index => $result) {
                $output .= '<tr><td>'.$index.'</td>';
                $output .= '<td>'.'<select name="_lecturer_id_'.$index.'">'.Users::select_options($results[$index]->lecturer_id).'</select></td>';
                $ExpireDate = wp_date( get_option( 'date_format' ), $results[$index]->expired_date );
                $output .= '<td><input type="text" name="_expired_date_'.$index.'" value="'.$ExpireDate.'">'.'</td></tr>';
            }
            $output .= '<tr><td>'.($index+1).'</td>';
            $output .= '<td>'.'<select name="_lecturer_id">'.Users::select_options().'</select>'.'</td>';
            $output .= '<td><input type="date" name="_expired_date"></td></tr>';
            $output .= '</tbody></table></figure>';
            
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'#'.'</td><td>'.'Witnesses'.'</td><td>Expired Date</td></tr>';
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_witnesses WHERE course_id = {$_id}", OBJECT );
            foreach ($results as $index => $result) {
                $output .= '<tr><td>'.$index.'</td>';
                $output .= '<td>'.'<select name="_witness_id_'.$index.'">'.Users::select_options($results[$index]->witness_id).'</select></td>';
                $ExpireDate = wp_date( get_option( 'date_format' ), $results[$index]->expired_date );
                $output .= '<td><input type="text" name="_w_expired_date_'.$index.'" value="'.$ExpireDate.'">'.'</td></tr>';
            }
            $output .= '<tr><td>'.($index+1).'</td>';
            $output .= '<td><select name="_witness_id">'.Users::select_options().'</select></td>';
            $output .= '<td><input type="date" name="_w_expired_date"></td></tr>';
            $output .= '</tbody></table></figure>';
            
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

        function list_mode() {
            
            if( isset($_GET['view_mode']) ) {
                return self::view_mode($_GET['_id']);
            }
            
            if( isset($_GET['edit_mode']) ) {
                return self::edit_mode($_GET['_id'], $_GET['edit_mode']);
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
        
                $output .= '<form method="get">';
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
        
            $output .= '<form method="get">';
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
            $output .= '<option value="delete_select">-- Remove this --</option>';
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

            $sql = "CREATE TABLE `{$wpdb->prefix}course_witnesses` (
                c_w_id int NOT NULL AUTO_INCREMENT,
                course_id int NOT NULL,
                witness_id int NOT NULL,
                expired_date int NOT NULL,
                PRIMARY KEY  (c_w_id)
            ) $charset_collate;";        
            dbDelta($sql);
        }
        
    }
    new courses();
}
?>