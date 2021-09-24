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

        function course_learnings( $_id=null ) {

            if ($_id==null){
                return '<div>course ID is required</div>';
            }

            if( isset($_POST['submit_action']) ) {
        
                global $wpdb;
                /** 
                 * submit learning
                 */
                $current_user_id = get_current_user_id();
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_course_learnings WHERE student_id = {$current_user_id} AND course_id = {$_id}", OBJECT );
                foreach ($results as $index => $result) {
                    if (( $_POST['_learning_id_'.$index]=='select_delete' ) || ( $_POST['_lecturer_witness_id_'.$index]=='select_delete' ) ){
                        $table = $wpdb->prefix.'user_course_learnings';
                        $where = array(
                            'u_c_l_id' => $results[$index]->u_c_l_id
                        );
                        $wpdb->delete( $table, $where );    
                    } else {
                        $table = $wpdb->prefix.'user_course_learnings';
                        $data = array(
                            'learning_id' => $_POST['_learning_id_'.$index],
                            'lecturer_witness_id' => $_POST['_lecturer_witness_id_'.$index],
                        );
                        $where = array(
                            'u_c_l_id' => $results[$index]->u_c_l_id
                        );
                        $wpdb->update( $table, $data, $where );    
                    }
                }
                if ( !($_POST['_learning_id']=='no_select') ){
                    $table = $wpdb->prefix.'user_course_learnings';
                    $data = array(
                        'student_id' => $current_user_id,
                        'course_id' => $_id,
                        'learning_id' => $_POST['_learning_id'],
                        'lecturer_witness_id' => $_POST['_lecturer_witness_id'],
                    );
                    $format = array('%d', '%d', '%d', '%d');
                    $wpdb->insert($table, $data, $format);
                }
            }

            /** 
             * course_learnings header
             */
            global $wpdb;
            $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}courses WHERE course_id = {$_id}", OBJECT );
            $CreateDate = wp_date( get_option( 'date_format' ), $row->created_date );
            $current_user_id = get_current_user_id();
            $output  = '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'Name:'.'</td><td>'.get_userdata($current_user_id)->display_name.'</td></tr>';
            $output .= '<tr><td>'.'Email:'.'</td><td>'.get_userdata($current_user_id)->user_email.'</td></tr>';
            $output .= '<tr><td>'.'Title:'.'</td><td>'.$row->course_title.'</td></tr>';
            //$output .= '<tr><td>'.'Created:'.'</td><td>'.$CreateDate.'</td></tr>';
            $output .= '</tbody></table></figure>';

            /** 
             * user course relationship with learning
             */
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'#'.'</td><td>Learnings</td><td>Lecturers/Witnesses</td></tr>';
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_course_learnings WHERE student_id = {$current_user_id} AND course_id = {$_id}", OBJECT );
            foreach ($results as $index => $result) {
                $output .= '<tr><td>'.($index+1).'</td>';
                $output .= '<td>'.'<select name="_learning_id_'.$index.'">'.self::select_learnings($_id, $results[$index]->learning_id).'</select></td>';
                $output .= '<td>'.'<select name="_lecturer_witness_id_'.$index.'">'.self::select_lecturers_witnesses($_id, $results[$index]->lecturer_witness_id).'</select></td>';
                //$ExpireDate = wp_date( get_option( 'date_format' ), $results[$index]->expired_date );
                //$output .= '<td><input type="text" name="_expired_date_'.$index.'" value="'.$ExpireDate.'">'.'</td></tr>';
            }
            $output .= '<tr><td>'.'#'.'</td>';
            $output .= '<td>'.'<select name="_learning_id">'.self::select_learnings($_id).'</select>'.'</td>';
            $output .= '<td>'.'<select name="_lecturer_witness_id">'.self::select_lecturers_witnesses($_id).'</select>'.'</td>';
            //$output .= '<td><input type="date" name="_expired_date"></td></tr>';
            $output .= '</tbody></table></figure>';
            
            /** 
             * course_learnings footer
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

        function view_mode( $_id=null ) {

            if ($_id==null){
                return '<div>course ID is required</div>';
            }

            if( isset($_POST['submit_action']) ) {
        
                /** 
                 * submit learning
                 */
                global $wpdb;
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE course_id = {$_id}", OBJECT );
                foreach ($results as $index => $result) {
                    if (( $_POST['_learning_title_'.$index]=='delete' ) || ( $_POST['_learning_link_'.$index]=='delete' ) ){
                        $table = $wpdb->prefix.'course_learnings';
                        $where = array(
                            'learning_id' => $results[$index]->learning_id
                        );
                        $wpdb->delete( $table, $where );    
                    } else {
                        $table = $wpdb->prefix.'course_learnings';
                        $data = array(
                            'learning_title' => $_POST['_learning_title_'.$index],
                            'learning_link' => $_POST['_learning_link_'.$index],
                        );
                        $where = array(
                            'learning_id' => $results[$index]->learning_id
                        );
                        $wpdb->update( $table, $data, $where );    
                    }
                }
                if ( !($_POST['_learning_title']=='') ){
                    $table = $wpdb->prefix.'course_learnings';
                    $data = array(
                        'learning_title' => $_POST['_learning_title'],
                        'learning_link' => $_POST['_learning_link'],
                        'course_id' => $_GET['_id']
                    );
                    $format = array('%s', '%s', '%d');
                    $wpdb->insert($table, $data, $format);
                }

                /** 
                 * submit lecturer
                 */
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_lecturers_witnesses WHERE course_id = {$_id} AND is_witness=false", OBJECT );
                foreach ($results as $index => $result) {
                    if ( $_POST['_lecturer_id_'.$index]=='delete_select' ){
                        $table = $wpdb->prefix.'course_lecturers_witnesses';
                        $where = array(
                            'c_l_w_id' => $results[$index]->c_l_w_id
                        );
                        $wpdb->delete( $table, $where );    
                    } else {
                        $table = $wpdb->prefix.'course_lecturers_witnesses';
                        $data = array(
                            'expired_date' => strtotime($_POST['_expired_date_'.$index]),
                            'lecturer_witness_id' => $_POST['_lecturer_id_'.$index]
                        );
                        $where = array(
                            'c_l_w_id' => $results[$index]->c_l_w_id
                        );
                        $wpdb->update( $table, $data, $where );    
                    }
                }
                if (!(( $_POST['_lecturer_id']=='no_select' ) || ( $_POST['_lecturer_id']=='delete_select' ))){
                    $table = $wpdb->prefix.'course_lecturers_witnesses';
                    $data = array(
                        'expired_date' => strtotime($_POST['_expired_date']), 
                        'lecturer_witness_id' => $_POST['_lecturer_id'],
                        'course_id' => $_GET['_id'],
                    );
                    $format = array('%d', '%d', '%d');
                    $wpdb->insert($table, $data, $format);
                }

                /** 
                 * submit witness
                 */
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_lecturers_witnesses WHERE course_id = {$_id} AND is_witness=true", OBJECT );
                foreach ($results as $index => $result) {
                    if ( $_POST['_witness_id_'.$index]=='delete_select' ){
                        $table = $wpdb->prefix.'course_lecturers_witnesses';
                        $where = array(
                            'c_l_w_id' => $results[$index]->c_l_w_id
                        );
                        $wpdb->delete( $table, $where );    
                    } else {
                        $table = $wpdb->prefix.'course_lecturers_witnesses';
                        $data = array(
                            'expired_date' => strtotime($_POST['_w_expired_date_'.$index]),
                            'lecturer_witness_id' => $_POST['_witness_id_'.$index]
                        );
                        $where = array(
                            'c_l_w_id' => $results[$index]->c_l_w_id
                        );
                        $wpdb->update( $table, $data, $where );    
                    }
                }
                if (!(( $_POST['_witness_id']=='no_select' ) || ( $_POST['_witness_id']=='delete_select' ))){
                    $table = $wpdb->prefix.'course_lecturers_witnesses';
                    $data = array(
                        'expired_date' => strtotime($_POST['_w_expired_date']), 
                        'lecturer_witness_id' => $_POST['_witness_id'],
                        'course_id' => $_GET['_id'],
                        'is_witness' => true,
                    );
                    //$format = array('%d', '%d', '%d');
                    //$wpdb->insert($table, $data, $format);
                    $wpdb->insert($table, $data);
                }
            }

            /** 
             * view_mode header
             */
            global $wpdb;
            $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}courses WHERE course_id = {$_id}", OBJECT );
            $CreateDate = wp_date( get_option( 'date_format' ), $row->created_date );
            $output  = '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'Title:'.'</td><td><a href="?view_mode=course_learnings&_id='.$_id.'">'.$row->course_title.'</a></td></tr>';
            $output .= '<tr><td>'.'Created:'.'</td><td>'.$CreateDate.'</td></tr>';
            $output .= '</tbody></table></figure>';

            /** 
             * course relationship with learnings
             */
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'#'.'</td><td>'.'Titles'.'</td><td>Link</td></tr>';
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE course_id = {$_id}", OBJECT );
            foreach ($results as $index => $result) {
                $output .= '<tr><td><a href="'.$results[$index]->learning_link.'">'.($index+1).'</a></td>';
                $output .= '<td><input size="20" type="text" name="_learning_title_'.$index.'" value="'.$results[$index]->learning_title.'"></td>';
                $output .= '<td><input size="50" type="text" name="_learning_link_'.$index.'" value="'.$results[$index]->learning_link.'"></td>';
                $output .= '</tr>';
            }
            $output .= '<tr><td>'.'#'.'</td>';
            $output .= '<td><input size="20" type="text" name="_learning_title"></td>';
            $output .= '<td><input size="50" type="text" name="_learning_link"></td>';
            $output .= '</tr></tbody></table></figure>';
            
            /** 
             * course relationship with lecturer 
             */
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'#'.'</td><td>'.'Lecturers'.'</td><td>Expired Date</td></tr>';
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_lecturers_witnesses WHERE course_id = {$_id} AND is_witness=false", OBJECT );
            foreach ($results as $index => $result) {
                $output .= '<tr><td>'.($index+1).'</td>';
                $output .= '<td>'.'<select name="_lecturer_id_'.$index.'">'.Users::select_options($results[$index]->lecturer_witness_id).'</select></td>';
                $ExpireDate = wp_date( get_option( 'date_format' ), $results[$index]->expired_date );
                $output .= '<td><input type="text" name="_expired_date_'.$index.'" value="'.$ExpireDate.'">'.'</td></tr>';
            }
            $output .= '<tr><td>'.'#'.'</td>';
            $output .= '<td>'.'<select name="_lecturer_id">'.Users::select_options().'</select>'.'</td>';
            $output .= '<td><input type="date" name="_expired_date"></td></tr>';
            $output .= '</tbody></table></figure>';
            
            /** 
             * course relationship with witness 
             */
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'#'.'</td><td>'.'Witnesses'.'</td><td>Expired Date</td></tr>';
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_lecturers_witnesses WHERE course_id = {$_id} AND is_witness=true", OBJECT );
            foreach ($results as $index => $result) {
                $output .= '<tr><td>'.($index+1).'</td>';
                $output .= '<td>'.'<select name="_witness_id_'.$index.'">'.Users::select_options($results[$index]->lecturer_witness_id).'</select></td>';
                $ExpireDate = wp_date( get_option( 'date_format' ), $results[$index]->expired_date );
                $output .= '<td><input type="text" name="_w_expired_date_'.$index.'" value="'.$ExpireDate.'">'.'</td></tr>';
            }
            $output .= '<tr><td>'.'#'.'</td>';
            $output .= '<td><select name="_witness_id">'.Users::select_options().'</select></td>';
            $output .= '<td><input type="date" name="_w_expired_date"></td></tr>';
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
                $_mode='Create';
            }

            if( isset($_POST['create_action']) ) {
        
                global $wpdb;
                $table = $wpdb->prefix.'courses';
                $data = array(
                    'created_date' => current_time('timestamp'), 
                    'course_title' => $_POST['_course_title']
                );
                $format = array('%d', '%s');
                $wpdb->insert($table, $data, $format);
                ?><script>window.location='/courses'</script><?php
            }
        
            if( isset($_POST['update_action']) ) {
        
                $UpdateCourseAction = new UpdateCourseAction();
                
                $UpdateCourseAction->setCourseId(intval($_POST['_course_id']));
                $UpdateCourseAction->setCourseTitle($_POST['_course_title']);
                $UpdateCourseAction->setCreatedDate(strtotime($_POST['_created_date']));
                //$UpdateCourseAction->setPublicKey($_POST['_public_key']);
                $send_data = $UpdateCourseAction->serializeToString();
/*                
                //$op_result = OP_RETURN_send($send_address, $send_amount, $send_data);
                $op_result = OP_RETURN_send(OP_RETURN_SEND_ADDRESS, OP_RETURN_SEND_AMOUNT, $send_data);
                //return var_dump($op_result);
            
                if (isset($op_result['error']))
                    $result_output = 'Error: '.$op_result['error']."\n";
                else {
                    $result_output = 'TxID: '.$op_result['txid']."\nWait a few seconds then check on: http://coinsecrets.org/\n";

                    global $wpdb;
                    $table = $wpdb->prefix.'courses';
                    $data = array(
                        'course_title' => $_POST['_course_title'],
                        'txid' => $op_result['txid'], 
                    );
                    $where = array('course_id' => $_POST['_course_id']);
                    $wpdb->update( $table, $data, $where );

                }
*/                
                ?><script>window.location='/courses'</script><?php

            }
        
            if( isset($_POST['delete_action']) ) {
        
                global $wpdb;
                $table = $wpdb->prefix.'courses';
                $where = array('course_id' => $_POST['_course_id']);
                $deleted = $wpdb->delete( $table, $where );
                ?><script>window.location='/courses'</script><?php
            }

            /** 
             * edit_mode
             */
            global $wpdb;
            $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}courses WHERE course_id = {$_id}", OBJECT );
            $CreateDate = wp_date( get_option( 'date_format' ), $row->created_date );
            $output  = '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            if( $_mode=='Update' ) {
                $output .= '<tr><td>'.'ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_course_id" value="'.$row->course_id.'"></td></tr>';
                $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_course_title" value="'.$row->course_title.'"></td></tr>';
                $output .= '<tr><td>'.'Created:'.'</td><td><input style="width: 100%" type="text" name="_created_date" value="'.$CreateDate.'" disabled></td></tr>';
            } else if( $_mode=='Delete' ) {
                $output .= '<tr><td>'.'ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_course_id" value="'.$row->course_id.'"></td></tr>';
                $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_course_title" value="'.$row->course_title.'" disabled></td></tr>';
                $output .= '<tr><td>'.'Created:'.'</td><td><input style="width: 100%" type="text" name="_created_date" value="'.$CreateDate.'" disabled></td></tr>';
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

        function list_mode() {
            
            if( isset($_GET['view_mode']) ) {
                if ($_GET['view_mode']=='course_learnings'){return self::course_learnings($_GET['_id']);}
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
                $CreateDate = wp_date( get_option( 'date_format' ), $results[$index]->created_date );
        
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

        function select_learnings( $course_id=null, $default_id=null ) {

            if ($course_id==null){
                $output = '<option value="no_select">-- course_id is required --</option>';
                return $output;    
            }
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE course_id={$course_id}", OBJECT );
            $output = '<option value="no_select">-- Select an option --</option>';
            foreach ($results as $index => $result) {
                if ( $results[$index]->learning_id == $default_id ) {
                    $output .= '<option value="'.$results[$index]->learning_id.'" selected>';
                } else {
                    $output .= '<option value="'.$results[$index]->learning_id.'">';
                }
                $output .= $results[$index]->learning_title;
                $output .= '</option>';        
            }
            $output .= '<option value="delete_select">-- Remove this --</option>';
            return $output;    
        }

        function select_lecturers_witnesses( $course_id=null, $default_id=null ) {

            if ($course_id==null){
                $output = '<option value="no_select">-- id is required --</option>';
                return $output;    
            }
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_lecturers_witnesses WHERE course_id={$course_id}", OBJECT );
            $output = '<option value="no_select">-- Select an option --</option>';
            foreach ($results as $index => $result) {
                if ( $results[$index]->lecturer_witness_id == $default_id ) {
                    $output .= '<option value="'.$results[$index]->lecturer_witness_id.'" selected>';
                } else {
                    $output .= '<option value="'.$results[$index]->lecturer_witness_id.'">';
                }
                $output .= get_userdata($results[$index]->lecturer_witness_id)->display_name;
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
                created_date int NOT NULL,
                public_key varchar(255),
                txid varchar(255),
                PRIMARY KEY  (course_id)
                is_deleted boolean,
            ) $charset_collate;";        
            dbDelta($sql);

            $sql = "CREATE TABLE `{$wpdb->prefix}course_learnings` (
                learning_id int NOT NULL AUTO_INCREMENT,
                course_id int NOT NULL,
                learning_title varchar(255),
                learning_link varchar(255),
                txid varchar(255),
                is_deleted boolean,
                PRIMARY KEY  (learning_id)
            ) $charset_collate;";        
            dbDelta($sql);
/*
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
*/
            $sql = "CREATE TABLE `{$wpdb->prefix}course_lecturers_witnesses` (
                c_l_w_id int NOT NULL AUTO_INCREMENT,
                course_id int NOT NULL,
                lecturer_witness_id int NOT NULL,
                expired_date int NOT NULL,
                is_witness boolean,
                txid varchar(255),
                is_deleted boolean,
                PRIMARY KEY  (c_l_w_id)
            ) $charset_collate;";        
            dbDelta($sql);
        }        
    }
    //if ( is_admin() )
    new courses();
}
?>