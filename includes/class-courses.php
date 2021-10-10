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
            add_shortcode('course-list', __CLASS__ . '::list_mode');
            //add_shortcode('course_edit', __CLASS__ . '::edit_mode');
            //add_shortcode('course_view', __CLASS__ . '::view_mode');
            self::create_tables();
        }

        function profit_sharing( $_id=null ) {

            if ($_id==null){
                return '<div>learning ID is required</div>';
            }

            if( isset($_POST['submit_action']) ) {
        
                global $wpdb;
                /** 
                 * submit
                 */
                $current_user_id = get_current_user_id();
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}learning_profit_sharing WHERE learning_id = {$_id}", OBJECT );
                foreach ($results as $index => $result) {
                    if (( $_POST['_sharing_id_'.$index]=='select_delete' )){
                        $table = $wpdb->prefix.'learning_profit_sharing';
                        $where = array(
                            'l_p_s_id' => $results[$index]->l_p_s_id
                        );
                        $wpdb->delete( $table, $where );    
                    } else {
                        $table = $wpdb->prefix.'learning_profit_sharing';
                        $data = array(
                            'sharing_title' => $_POST['_sharing_title_'.$index],
                            'sharing_id' => $_POST['_sharing_id_'.$index],
                            'sharing_profit' => $_POST['_sharing_profit_'.$index],
                        );
                        $where = array(
                            'l_p_s_id' => $results[$index]->l_p_s_id
                        );
                        $wpdb->update( $table, $data, $where );    
                    }
                }
                if ( !($_POST['_sharing_title']=='') ){
                    $table = $wpdb->prefix.'learning_profit_sharing';
                    $data = array(
                        'learning_id' => $_id,
                        'sharing_id' => $_POST['_sharing_id'],
                        'sharing_title' => $_POST['_sharing_title'],
                        'sharing_profit' => $_POST['_sharing_profit'],
                    );
                    $format = array('%d', '%d', '%s', '%f');
                    $wpdb->insert($table, $data, $format);
                }
            }

            /** 
             * profit_sharing header
             */
            global $wpdb;
            $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE learning_id = {$_id}", OBJECT );
            $product = wc_get_product( $row->course_id );

            $output  = '<h2>課程成本結構設定</h2>';
            $output .= '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'Course:'.'</td><td>'.$product->get_name().'</td></tr>';
            $output .= '<tr><td>'.'Learning:'.'</td><td>'.$row->learning_title.'</td></tr>';
            $output .= '</tbody></table></figure>';

            /** 
             * profit sharing relationship with learning
             */
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'#'.'</td><td>Titles</td><td>Sharing</td><td>Profit</td></tr>';
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}learning_profit_sharing WHERE learning_id = {$_id}", OBJECT );
            foreach ($results as $index => $result) {
                $output .= '<tr><td>'.($index+1).'</td>';
                $output .= '<td><input size="20" type="text" name="_sharing_title_'.$index.'" value="'.$results[$index]->sharing_title.'"></td>';
                $output .= '<td>'.'<select name="_sharing_id_'.$index.'">'.users::select_options($results[$index]->sharing_id).'</select></td>';
                $output .= '<td><input size="5" type="text" name="_sharing_profit_'.$index.'" value="'.$results[$index]->sharing_profit.'"></td>';
                $output .= '</tr>';
            }
            $output .= '<tr><td>'.'#'.'</td>';
            $output .= '<td><input size="20" type="text" name="_sharing_title"></td>';
            $output .= '<td>'.'<select name="_sharing_id">'.users::select_options().'</select>'.'</td>';
            $output .= '<td><input size="5" type="text" name="_sharing_profit"></td>';
            $output .= '</tr></tbody></table></figure>';
            
            /** 
             * profit sharing footer
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

        function course_learnings( $_id=null ) {

            if ($_id==null){
                return '<div>course ID is required</div>';
            }

            if( isset($_POST['submit_action']) ) {        
                /** 
                 * submit
                 */
                $current_user_id = get_current_user_id();
                global $wpdb;
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
                            'lecturer_id' => $_POST['_lecturer_id_'.$index],
                            'witness_id' => $_POST['_witness_id_'.$index],
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
                        'learning_id' => $_POST['_learning_id'],
                        'lecturer_id' => $_POST['_lecturer_id'],
                        'witness_id' => $_POST['_witness_id'],
                        'course_id' => $_id,
                    );
                    $format = array('%d', '%d', '%d', '%d', '%d');
                    $wpdb->insert($table, $data, $format);
                }
            }

            /** 
             * course_learnings header
             */
            $current_user_id = get_current_user_id();
            $product = wc_get_product( $_id );

            $output  = '<h2>個人學習項目的輔導與認證</h2>';
            $output .= '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'Name:'.'</td><td>'.get_userdata($current_user_id)->display_name.'</td></tr>';
            $output .= '<tr><td>'.'Email:'.'</td><td>'.get_userdata($current_user_id)->user_email.'</td></tr>';
            $output .= '<tr><td>'.'Title:'.'</td><td>'.$product->get_name().'</td></tr>';
            $output .= '</tbody></table></figure>';

            /** 
             * user course relationship with learning
             */
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_course_learnings WHERE student_id = {$current_user_id} AND course_id = {$_id}", OBJECT );
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'#'.'</td><td>Learnings</td><td>Lecturers</td><td>Witnesses</td></tr>';
            foreach ($results as $index => $result) {
                $output .= '<tr><td>'.($index+1).'</td>';
                $output .= '<td>'.'<select name="_learning_id_'.$index.'">'.self::select_learnings($_id, $results[$index]->learning_id).'</select></td>';
                $output .= '<td>'.'<select name="_lecturer_id_'.$index.'">'.self::select_lecturers($results[$index]->learning_id, $results[$index]->lecturer_id).'</select></td>';
                $output .= '<td>'.'<select name="_witness_id_'.$index.'">'.self::select_witnesses($results[$index]->learning_id, $results[$index]->witness_id).'</select></td>';
            }
            $output .= '<tr><td>'.'#'.'</td>';
            $output .= '<td>'.'<select name="_learning_id">'.self::select_learnings($_id).'</select>'.'</td>';
            $output .= '<td></td><td></td>';
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
                 * submit
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
                            'learning_hours' => $_POST['_learning_hours_'.$index],
                            'learning_link' => $_POST['_learning_link_'.$index],
                            'teaching_id' => $_POST['_teaching_id_'.$index],
                            'is_witness' => rest_sanitize_boolean($_POST['_is_witness_'.$index]),
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
                        'course_id' => intval($_GET['_id']),
                        'learning_title' => sanitize_text_field($_POST['_learning_title']),
                        'learning_hours' => floatval($_POST['_learning_hours']),
                        'learning_link' => sanitize_text_field($_POST['_learning_link']),
                        'teaching_id' => intval($_POST['_teaching_id']),
                        'is_witness' => rest_sanitize_boolean($_POST['_is_witness']),
                    );
                    $format = array('%d', '%s', '%f', '%s', '%d', '%d');
                    $wpdb->insert($table, $data, $format);
                }
            }

            /** 
             * view_mode header
             */
            $product = wc_get_product( $_id );
            $output  = '<h2>課程vs學習項目設定</h2>';
            $output .= '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'Title:'.'</td><td><a href="?view_mode=course_learnings&_id='.$_id.'">'.$product->get_name().'</a></td></tr>';
            $output .= '<tr><td>'.'Created:'.'</td><td>'.$product->get_date_created().'</td></tr>';
            $output .= '<tr><td>'.'List Price:'.'</td><td>'.$product->get_regular_price().'</td></tr>';
            $output .= '<tr><td>'.'Sale Price:'.'</td><td>'.$product->get_sale_price().'</td></tr>';
            $output .= '</tbody></table></figure>';
            //return $output;

            /** 
             * course relationship with learnings
             */
            $TotalHours=0;
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE course_id = {$_id}", OBJECT );
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'#'.'</td><td>'.'Titles'.'</td><td>Hours</td><td>Link</td><td>Lecture</td><td>Witness</td></tr>';
            foreach ($results as $index => $result) {
                
                $output .= '<tr><td><a href="?view_mode=profit_sharing&_id='.$results[$index]->learning_id.'">'.($index+1).'</a></td>';
                $output .= '<td><input size="20" type="text" name="_learning_title_'.$index.'" value="'.$results[$index]->learning_title.'"></td>';
                $output .= '<td><input size="1" type="text" name="_learning_hours_'.$index.'" value="'.$results[$index]->learning_hours.'"></td>';
                $output .= '<td><input size="50" type="text" name="_learning_link_'.$index.'" value="'.$results[$index]->learning_link.'"></td>';
                $output .= '<td><select name="_teaching_id_'.$index.'" style="max-width:80px;">'.self::select_teachings($results[$index]->teaching_id).'</select></td>';
                $output .= '<td><input type="checkbox" name="_is_witness_'.$index.'"';
                if ($results[$index]->is_witness) {$output .= ' value="true" checked';}
                $output .= '></td>';
                $output .= '</tr>';
                $TotalHours += floatval($results[$index]->learning_hours);
                
            }
            $output .= '<tr><td>'.'#'.'</td>';
            $output .= '<td><input size="20" type="text" name="_learning_title"></td>';
            $output .= '<td><input size="1" type="text" name="_learning_hours"></td>';
            $output .= '<td><input size="50" type="text" name="_learning_link"></td>';
            $output .= '<td><select name="_teaching_id" style="max-width:80px;">'.self::select_teachings().'</select>'.'</td>';
            $output .= '<td><input type="checkbox" name="_is_witness"></td>';
            $output .= '</tr>';
            $output .= '<tr><td colspan=2>'.'Total Hours:'.'</td>';
            $output .= '<td>'.$TotalHours.'</td><td></td>';
            $output .= '</tr></tbody></table></figure>';            

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

            /** 
             * edit_mode
             */
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

        function list_mode() {
            
            if( isset($_GET['view_mode']) ) {
                if ($_GET['view_mode']=='profit_sharing'){return self::profit_sharing($_GET['_id']);}
                if ($_GET['view_mode']=='course_learnings'){return self::course_learnings($_GET['_id']);}
                return self::view_mode($_GET['_id']);
            }
            
            if( isset($_GET['edit_mode']) ) {
                return self::edit_mode($_GET['_id'], $_GET['edit_mode']);
            }            

            /**
             * List Mode
             */
            $args = array(
                'post_type'      => 'product',
                //'posts_per_page' => 10,
                'product_cat'    => 'Courses'
            );
        
            $loop = new WP_Query( $args );
        
            $output  = '<h2>課程列表</h2>';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>Title</td><td>Price</td><td></td><td></td></tr>';
            while ( $loop->have_posts() ) : $loop->the_post();
                global $product;
                $output .= '<form method="get">';
                $output .= '<tr>';
                $output .= '<td><a href="?view_mode=true&_id='.$product->get_id().'">'.$product->get_name().'</a></td>';
                $output .= '<td>'.$product->get_price().'</td>';
                $output .= '<input type="hidden" value="'.$product->get_id().'" name="_id">';
                //$output .= '<td><input class="wp-block-button__link" type="submit" value="Update" name="edit_mode"></td>';
                //$output .= '<td><input class="wp-block-button__link" type="submit" value="Delete" name="edit_mode"></td>';
                $output .= '</tr>';
                $output .= '</form>';
            endwhile;
            $output .= '</tbody></table></figure>';
/*        
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
*/        
            wp_reset_query();
            return $output;
        }
        
        function select_options( $default_id=null ) {

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
            return $output;
/*
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
*/            
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
                //$row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}courses WHERE course_id={$results[$index]->course_id}", OBJECT );
                //$output .= $results[$index]->learning_title . '('. $row->course_title . ')';
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

        function create_tables() {
        
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
/*        
            $sql = "CREATE TABLE `{$wpdb->prefix}courses` (
                course_id int NOT NULL AUTO_INCREMENT,
                course_title varchar(255) NOT NULL,
                created_date int NOT NULL,
                list_price float,
                sale_price float,
                public_key varchar(255),
                txid varchar(255),
                is_deleted boolean,
                PRIMARY KEY  (course_id)
            ) $charset_collate;";        
            dbDelta($sql);
*/
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

        }        
    }
    //if ( is_admin() )
    new courses();
}
?>