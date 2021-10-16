<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('certifications')) {

    class certifications {

        /**
         * Class constructor
         */
        public function __construct() {
            add_shortcode('certification_list', __CLASS__ . '::list_mode');
            add_shortcode('certification-list', __CLASS__ . '::list_mode');
            //add_shortcode('course_edit', __CLASS__ . '::edit_mode');
            //add_shortcode('course_view', __CLASS__ . '::view_mode');
            self::create_tables();
        }

        function view_mode( $_id=null ) {

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

                    $op_result = OP_RETURN_send(OP_RETURN_SEND_ADDRESS, OP_RETURN_SEND_AMOUNT, $send_data);
                    //return var_dump($op_result);
                
                    if (isset($op_result['error'])) {

                        $result_output = 'Error: '.$op_result['error']."\n";
                        return $result_output;
                    }
                    else {
                        $result_output = 'TxID: '.$op_result['txid']."\nWait a few seconds then check on: http://coinsecrets.org/\n";

                        global $wpdb;
                        $table = $wpdb->prefix.'user_course_learnings';
                        $data = array(
                            'learning_date' => strtotime($_POST['_learning_date_'.$index]), 
                            'txid' => $op_result['txid'], 
                        );
                        $where = array(
                            'u_c_l_id' => $results[$index]->u_c_l_id
                        );
                        $wpdb->update( $table, $data, $where );
    
                    }
                }
            }
            
            /** 
             * view_mode header
             */
            $output  = '<h2>個人學習歷程</h2>';
            $output .= '<form method="post">';
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
                if ($course_id == $results[$index]->course_id) $course_header=false;
                if ($course_header) {
                    $course_id = $results[$index]->course_id;
                    $product = wc_get_product( $results[$index]->course_id );
                    $output .= '<tr><td colspan="4">'.$product->get_name().'</td></td>';
                    $output .= '<tr><td>#</td><td>Learnings</td><td>Lecturer</td><td>Date</td><td>Witness</td><td>Date</td></tr>';
                }

                $lectureDate = wp_date( get_option( 'date_format' ), $results[$index]->lecture_date );
                $certifidDate = wp_date( get_option( 'date_format' ), $results[$index]->certifid_date );
                $output .= '<tr><td>'.$index.'</td>';
                $learning_id = $results[$index]->learning_id;
                $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE learning_id = {$learning_id}", OBJECT );
                $output .= '<td>'.$row->learning_title.'</td>';
                $output .= '<td>'.get_userdata($results[$index]->lecturer_id)->display_name.'</td>';
                $output .= '<td><input type="text" name="_lecture_date_'.$index.'" value="'.$lectureDate.'">'.'</td>';
                $output .= '<td>'.get_userdata($results[$index]->witness_id)->display_name.'</td>';
                $output .= '<td><input type="text" name="_certifid_date_'.$index.'" value="'.$certifidDate.'">'.'</td>';
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
/*
        function edit_mode( $_id=null, $_mode ) {

            if ($_id==null){
                $_mode='Create';
            }

            if( isset($_POST['create_action']) ) {
        
                global $wpdb;
                $table = $wpdb->prefix.'courses';
                $data = array(
                    'created_date' => current_time('timestamp'), 
                    'course_title' => $_POST['_course_title'],
                    'list_price' => $_POST['_list_price'],
                    'sale_price' => $_POST['_sale_price'],
                );
                $format = array('%d', '%s', '%f', '%f');
                $insert_id = $wpdb->insert($table, $data, $format);

                $CreateCourseAction = new CreateCourseAction();                
                //$CreateCourseAction->setCourseId(intval($_POST['_course_id']));
                $CreateCourseAction->setCourseId(intval($insert_id));
                $CreateCourseAction->setCourseTitle($_POST['_course_title']);
                $CreateCourseAction->setCreatedDate(intval(current_time('timestamp')));
                //$CreateCourseAction->setListPrice(floatval($_POST['_list_price']));
                //$CreateCourseAction->setSalePrice(floadval($_POST['_sale_price']));
                $CreateCourseAction->setPublicKey($_POST['_public_key']);
                $send_data = $CreateCourseAction->serializeToString();

                $op_result = OP_RETURN_send(OP_RETURN_SEND_ADDRESS, OP_RETURN_SEND_AMOUNT, $send_data);
            
                if (isset($op_result['error'])) {

                    $result_output = 'Error: '.$op_result['error']."\n";
                    return $result_output;
                } else {

                    $table = $wpdb->prefix.'courses';
                    $data = array(
                        'txid' => $op_result['txid'], 
                    );
                    $where = array('course_id' => $insert_id);
                    $wpdb->update( $table, $data, $where );
                }

                ?><script>window.location='/courses'</script><?php
            }
        
            if( isset($_POST['update_action']) ) {
        
                $UpdateCourseAction = new UpdateCourseAction();                
                $UpdateCourseAction->setCourseId(intval($_POST['_course_id']));
                $UpdateCourseAction->setCourseTitle($_POST['_course_title']);
                $UpdateCourseAction->setCreatedDate(intval(strtotime($_POST['_created_date'])));
                //$UpdateCourseAction->setListPrice(floatval($_POST['_list_price']));
                //$UpdateCourseAction->setSalePrice(floatval($_POST['_sale_price']));
                $UpdateCourseAction->setPublicKey($_POST['_public_key']);
                $send_data = $UpdateCourseAction->serializeToString();

                $op_result = OP_RETURN_send(OP_RETURN_SEND_ADDRESS, OP_RETURN_SEND_AMOUNT, $send_data);
            
                if (isset($op_result['error'])) {
                    $result_output = 'Error: '.$op_result['error']."\n";
                    return $result_output;
                } else {

                    global $wpdb;
                    $table = $wpdb->prefix.'courses';
                    $data = array(
                        'course_title' => $_POST['_course_title'],
                        'list_price' => $_POST['_list_price'],
                        'sale_price' => $_POST['_sale_price'],
                        'txid' => $op_result['txid'], 
                    );
                    $where = array('course_id' => $_POST['_course_id']);
                    $wpdb->update( $table, $data, $where );
                }

                ?><script>window.location='/courses'</script><?php
            }
        
            if( isset($_POST['delete_action']) ) {
        
                global $wpdb;
                $table = $wpdb->prefix.'courses';
                $where = array('course_id' => $_POST['_course_id']);
                $deleted = $wpdb->delete( $table, $where );
                ?><script>window.location='/courses'</script><?php
            }
*/
            /** 
             * edit_mode
             */
/*            
            global $wpdb;
            $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}courses WHERE course_id = {$_id}", OBJECT );
            $CreatedDate = wp_date( get_option( 'date_format' ), $row->created_date );
            $output  = '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            if( $_mode=='Update' ) {
                $output .= '<tr><td>'.'ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_course_id" value="'.$row->course_id.'"></td></tr>';
                $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_course_title" value="'.$row->course_title.'"></td></tr>';
                $output .= '<tr><td>'.'Created:'.'</td><td><input style="width: 100%" type="text" name="_created_date" value="'.$CreatedDate.'" disabled></td></tr>';
                $output .= '<tr><td>'.'List Price:'.'</td><td><input style="width: 100%" type="text" name="_list_price" value="'.$row->list_price.'"></td></tr>';
                $output .= '<tr><td>'.'Sale Price:'.'</td><td><input style="width: 100%" type="text" name="_sale_price" value="'.$row->list_price.'"></td></tr>';
                $output .= '<tr><td>'.'TxID:'.'</td><td><input style="width: 100%" type="text" name="_txid" value="'.$row->txid.'" disabled></td></tr>';
            } else if( $_mode=='Delete' ) {
                $output .= '<tr><td>'.'ID:'.'</td><td style="width: 100%"><input style="width: 100%" type="text" name="_course_id" value="'.$row->course_id.'"></td></tr>';
                $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_course_title" value="'.$row->course_title.'" disabled></td></tr>';
                $output .= '<tr><td>'.'Created:'.'</td><td><input style="width: 100%" type="text" name="_created_date" value="'.$CreatedDate.'" disabled></td></tr>';
                $output .= '<tr><td>'.'List Price:'.'</td><td><input style="width: 100%" type="text" name="_list_price" value="'.$row->list_price.'" disabled></td></tr>';
                $output .= '<tr><td>'.'Sale Price:'.'</td><td><input style="width: 100%" type="text" name="_sale_price" value="'.$row->list_price.'" disabled></td></tr>';
                $output .= '<tr><td>'.'TxID:'.'</td><td><input style="width: 100%" type="text" name="_txid" value="'.$row->txid.'" disabled></td></tr>';
            } else if( $_mode=='Create' ){
                $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_course_title" value=""></td></tr>';
                $output .= '<tr><td>'.'List Price:'.'</td><td><input style="width: 100%" type="text" name="_list_price" value=""></td></tr>';
                $output .= '<tr><td>'.'Sale Price:'.'</td><td><input style="width: 100%" type="text" name="_sale_price" value=""></td></tr>';
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
*/
        function list_mode() {
            
            if( isset($_GET['view_mode']) ) {
                return self::view_mode($_GET['_id']);
            }
/*            
            if( isset($_GET['edit_mode']) ) {
                return self::edit_mode($_GET['_id'], $_GET['edit_mode']);
            }            
*/
            /**
             * List Mode
             */
            $args = array(
                'post_type'      => 'product',
                'product_cat'    => 'Certifications'
            );
        
            $loop = new WP_Query( $args );
        
            global $wpdb;
            $output  = '<h2>認證項目列表</h2>';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<ul>';
            while ( $loop->have_posts() ) : $loop->the_post();
                global $product;
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE course_id = {$product->get_id()}", OBJECT );
                foreach ($results as $index => $result) {
                    
                    $output .= '<tr><td>'.$results[$index]->learning_title.'('.$results[$index]->learning_id.')</td></tr>';
                    $c_results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE teaching_id = {$results[$index]->learning_id}", OBJECT );
                    foreach ($c_results as $c_index => $result) {
                        //$output .= '<tr><td><a href="?view_mode=profit_sharing&_id='.$c_results[$c_index]->learning_id.'">'.$c_results[$c_index]->learning_title.'</a></td></tr>';
                        $u_results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_course_learnings WHERE learning_id = {$c_results[$c_index]->learning_id} ORDER BY student_id", OBJECT );
                        $first_line=true;
                        foreach ($u_results as $u_index => $result) {
                            if ($student_id==$u_results[$u_index]->student_id) $first_line=false;
                            if ($first_line) {

                                $output .= '<tr><td><li><a href="?view_mode=true&_id='.$u_results[$u_index]->student_id.'">'.get_userdata($u_results[$u_index]->student_id)->display_name.'</a></td></tr>';
                                $student_id=$u_results[$u_index]->student_id;
                            }
                        }
                    }
                }
            endwhile;
            $output .= '</ul>';
            $output .= '</tbody></table></figure>';

            wp_reset_query();
            return $output;
        }

        function select_options( $learning_id, $default_id=null ) {

            if ($learning_id==null){
                $output = '<option value="no_select">-- $learning_id is required --</option>';
                return $output;    
            }
            global $wpdb;
            $c_results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE teaching_id = {$learning_id}", OBJECT );
            foreach ($c_results as $c_index => $result) {
                $u_results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_course_learnings WHERE learning_id = {$c_results[$c_index]->learning_id} ORDER BY student_id", OBJECT );
                $first_line=true;
                foreach ($u_results as $u_index => $result) {
                    if ($student_id==$u_results[$u_index]->student_id) $first_line=false;
                    if ($first_line) {
                        if ( $u_results[$u_index]->student_id == $default_id ) {
                            $output .= '<option value="'.$u_results[$u_index]->student_id.'" selected>';
                        } else {
                            $output .= '<option value="'.$u_results[$u_index]->student_id.'">';
                        }
                        $output .= get_userdata($u_results[$u_index]->student_id)->display_name;
                        $output .= '</option>';        
                        $student_id=$u_results[$u_index]->student_id;
                    }
                }
            }
/*
            $args = array(
                'post_type'      => 'product',
                //'posts_per_page' => 10,
                'product_cat'    => 'Courses'
            );       
            $loop = new WP_Query( $args );
        
            $output = '<option value="no_select">-- Select an option --</option>';
            while ( $loop->have_posts() ) : $loop->the_post();
                global $product;
                if ( $product->get_id() == $default_id ) {
                    $output .= '<option value="'.$product->get_id().'" selected>';
                } else {
                    $output .= '<option value="'.$product->get_id().'">';
                }
                $output .= $product->get_name();
                $output .= '</option>';        
            endwhile;
            $output .= '<option value="delete_select">-- Remove this --</option>';

            wp_reset_query();
*/            
            return $output;
        }
/*
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

        function select_teachings( $default_id=null ) {

            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_learnings", OBJECT );
            $output = '<option value="no_select">-- Select an option --</option>';
            foreach ($results as $index => $result) {
                if ( $results[$index]->learning_id == $default_id ) {
                    $output .= '<option value="'.$results[$index]->learning_id.'" selected>';
                } else {
                    $output .= '<option value="'.$results[$index]->learning_id.'">';
                }
                $product = wc_get_product( $results[$index]->course_id );
                $output .= $results[$index]->learning_title . '('. $product->get_name() . ')';
                $output .= '</option>';        
            }
            $output .= '<option value="delete_select">-- Remove this --</option>';
            return $output;    
        }

        function select_lecturers( $learning_id=null, $default_id=null ) {

            if ($learning_id==null){
                $output = '<option value="no_select">-- learning id is required --</option>';
                return $output;    
            }
            global $wpdb;
            $t_results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE teaching_id={$learning_id}", OBJECT );
            $output = '<option value="no_select">-- Select an option --</option>';
            foreach ($t_results as $t_index => $t_result) {
                $t_learning_id = $t_results[$t_index]->learning_id;
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_course_learnings WHERE learning_id={$t_learning_id}", OBJECT );
                foreach ($results as $index => $result) {
                    if ( $results[$index]->student_id == $default_id ) {
                        $output .= '<option value="'.$results[$index]->student_id.'" selected>';
                    } else {
                        $output .= '<option value="'.$results[$index]->student_id.'">';
                    }
                    $output .= get_userdata($results[$index]->student_id)->display_name;
                    $output .= '</option>';        
                }
                $output .= '<option value="delete_select">-- Remove this --</option>';
            }
            return $output;    
        }

        function select_witnesses( $learning_id=null, $default_id=null ) {

            if ($learning_id==null){
                $output = '<option value="no_select">-- learning id is required --</option>';
                return $output;    
            }
            global $wpdb;
            $t_results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE teaching_id={$learning_id} AND is_witness", OBJECT );
            $output = '<option value="no_select">-- Select an option --</option>';
            foreach ($t_results as $t_index => $t_result) {
                $t_learning_id = $t_results[$t_index]->learning_id;
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_course_learnings WHERE learning_id={$t_learning_id}", OBJECT );
                foreach ($results as $index => $result) {
                    if ( $results[$index]->student_id == $default_id ) {
                        $output .= '<option value="'.$results[$index]->student_id.'" selected>';
                    } else {
                        $output .= '<option value="'.$results[$index]->student_id.'">';
                    }
                    $output .= get_userdata($results[$index]->student_id)->display_name;
                    $output .= '</option>';        
                }
                $output .= '<option value="delete_select">-- Remove this --</option>';
            }
            return $output;    
        }
*/
        function create_tables() {
        
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
/*        
            $sql = "CREATE TABLE `{$wpdb->prefix}course_learnings` (
                learning_id int NOT NULL AUTO_INCREMENT,
                course_id int NOT NULL,
                learning_hours float DEFAULT 1.0,
                learning_title varchar(255),
                learning_link varchar(255),
                teaching_id int DEFAULT 0,
                is_witness boolean,
                txid varchar(255),
                is_deleted boolean,
                PRIMARY KEY  (learning_id)
            ) $charset_collate;";        
            dbDelta($sql);

            $sql = "CREATE TABLE `{$wpdb->prefix}user_course_learnings` (
                u_c_l_id int NOT NULL AUTO_INCREMENT,
                student_id int NOT NULL,
                learning_id int,
                lecturer_id int,
                lecture_date int,
                witness_id int,
                certified_date int,
                txid varchar(255),
                is_deleted boolean,
                course_id int,
                teaching_id int,
                PRIMARY KEY  (u_c_l_id)
            ) $charset_collate;";        
            dbDelta($sql);

            $sql = "CREATE TABLE `{$wpdb->prefix}learning_profit_sharing` (
                l_p_s_id int NOT NULL AUTO_INCREMENT,
                learning_id int NOT NULL,
                sharing_title varchar(255),
                sharing_id int,
                sharing_profit float,
                txid varchar(255),
                is_deleted boolean,
                PRIMARY KEY  (l_p_s_id)
            ) $charset_collate;";        
            dbDelta($sql);
*/
        }        
    }
    //if ( is_admin() )
    new certifications();
}
?>