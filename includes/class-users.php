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

        function view_mode($_id=null) {

            if ($_id==null){
                $_id=get_current_user_id();
            }

            if( isset($_POST['submit_action']) ) {
        
                global $wpdb;
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_courses WHERE student_id = {$_id}", OBJECT );
                foreach ($results as $index => $result) {
                    if ( $_POST['_course_id_'.$index]=='delete_select' ){
                        $table = $wpdb->prefix.'user_courses';
                        $where = array(
                            't_c_id' => $results[$index]->t_c_id
                        );
                        $wpdb->delete( $table, $where );    
                    } else {
                        $table = $wpdb->prefix.'user_courses';
                        $data = array(
                            'certification_date' => strtotime($_POST['_certification_date_'.$index]), 
                            'lecturer_id' => $_POST['_lecturer_id_'.$index],
                            'witness_id' => $_POST['_witness_id_'.$index],
                            'course_id' => $_POST['_course_id_'.$index],
                        );
                        $where = array(
                            't_c_id' => $results[$index]->t_c_id
                        );
                        $wpdb->update( $table, $data, $where );
                    }
                }
                if (( $_POST['_course_id']=='no_select' ) || ( $_POST['_course_id']=='delete_select' ) ){
                } else {
                    $table = $wpdb->prefix.'user_courses';
                    $data = array(
                        'certification_date' => strtotime($_POST['_certification_date']), 
                        'course_id' => $_POST['_course_id'],
                        'lecturer_id' => $_POST['_lecturer_id'],
                        'witness_id' => $_POST['_witness_id'],
                        'student_id' => $_id,
                    );
                    $format = array('%d', '%d');
                    $wpdb->insert($table, $data, $format);    
                }
            }
            
            /** 
             * view_mode
             */
            $output  = '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'Name:'.'</td><td>'.get_userdata($_id)->display_name.'</td></tr>';
            $output .= '<tr><td>'.'Email:'.'</td><td>'.get_userdata($_id)->user_email.'</td></tr>';
            $output .= '</tbody></table></figure>';

            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'#'.'</td><td>'.'Courses'.'</td><td>Lecturers</td><td>Witnesses</td><td>Certification</td></tr>';
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_courses WHERE student_id = {$_id}", OBJECT );
            foreach ($results as $index => $result) {
                $output .= '<tr><td>'.$index.'</td>';
                $output .= '<td><select name="_course_id_'.$index.'">'.Courses::select_options($results[$index]->course_id).'</select></td>';
                $output .= '<td><select name="_lecturer_id_'.$index.'">'.self::select_options($results[$index]->lecturer_id).'</select></td>';
                //$output .= '<td><select name="_lecturer_id_'.$index.'">'.Courses::select_leturers($results[$index]->lecturer_id).'</select></td>';
                $output .= '<td><select name="_witness_id_'.$index.'">'.self::select_options($results[$index]->witness_id).'</select></td>';
                //$output .= '<td><select name="_witness_id_'.$index.'">'.Courses::select_witnesses($results[$index]->witness_id).'</select></td>';
                $CertificationDate = wp_date( get_option( 'date_format' ), $results[$index]->certification_date );
                $output .= '<td><input type="text" name="_certification_date_'.$index.'" value="'.$CertificationDate.'">'.'</td></tr>';
            }
            $output .= '<tr><td>'.($index+1).'</td>';
            $output .= '<td><select name="_course_id">'.Courses::select_options().'</select></td>';
            //$output .= '<td><select name="_lecturer_id">'.self::select_options().'</select></td>';
            $output .= '<td><select name="_lecturer_id">'.Courses::select_leturers().'</select></td>';
            $output .= '<td><select name="_witness_id">'.self::select_options().'</select></td>';
            //$output .= '<td><select name="_witness_id">'.Courses::select_witnesses.'</select></td>';
            $output .= '<td><input type="date" name="_certification_date"></td></tr>';
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
        
    }
    new users();
}
?>