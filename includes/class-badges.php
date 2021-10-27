<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('badges')) {

    class badges {

        /**
         * Class constructor
         */
        public function __construct() {
            add_shortcode('badge_list', __CLASS__ . '::list_mode');
            add_shortcode('badge-list', __CLASS__ . '::list_mode');
            //add_shortcode('course_edit', __CLASS__ . '::edit_mode');
            //add_shortcode('course_view', __CLASS__ . '::view_mode');
            self::create_tables();
            wp_insert_term( 'Badges', 'product_cat', array(
                'description' => 'Description for category', // optional
                'parent' => 0, // optional
                'slug' => 'badges' // optional
            ) );
            
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
            $output .= '<tr><td>'.'Learning:'.'</td><td><a href="'.$row->learning_link.'">'.$row->learning_title.'</a></td></tr>';
            $output .= '</tbody></table></figure>';
            //return $output;

            /** 
             * profit sharing relationship with learning
             */
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'#'.'</td><td>Titles</td><td>Sharing</td><td>Profit</td></tr>';
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}learning_profit_sharing WHERE learning_id = {$_id}", OBJECT );
            foreach ($results as $index => $result) {
                $output .= '<tr><td>'.($index+1).'</td>';
                $output .= '<td><input size="20" type="text" name="_sharing_title_'.$index.'" value="'.$results[$index]->sharing_title.'"></td>';
                $output .= '<td>'.'<select name="_sharing_id_'.$index.'">'.self::select_users($results[$index]->sharing_id).'</select></td>';
                $output .= '<td><input size="5" type="text" name="_sharing_profit_'.$index.'" value="'.$results[$index]->sharing_profit.'"></td>';
                $output .= '</tr>';
            }
            $output .= '<tr><td>'.'#'.'</td>';
            $output .= '<td><input size="20" type="text" name="_sharing_title"></td>';
            $output .= '<td>'.'<select name="_sharing_id">'.self::select_users().'</select>'.'</td>';
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
            //$output .= '<button class="wp-block-button__link" onclick="location.href=`javascript:history.go(-1)`">Back</button>';
            //$output .= '<a href="javascript:history.go(-1)">Back</a>';
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
            $output .= '<tr><td>'.'Title:'.'</td><td>'.$product->get_name().'</td></tr>';
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
            $output .= '<tr><td>'.'#'.'</td><td>'.'Titles'.'</td><td>Hours</td><td>Link</td><td>Mentor</td><td>Witness</td></tr>';
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

        function list_mode() {
            
            if( isset($_GET['view_mode']) ) {
                if ($_GET['view_mode']=='profit_sharing') return self::profit_sharing($_GET['_id']);
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
                'product_cat'    => 'Badges'
            );
                
            $output  = '<h2>教師考取相關證照紀錄</h2>';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>證照紀錄</td>';
            $loop = new WP_Query( $args );
            while ( $loop->have_posts() ) : $loop->the_post();
                global $product;
                $output .= '<td><a href="?view_mode=badge&_id='.$product->get_id().'">'.$product->get_name().'</a></td>';
            endwhile;
            wp_reset_query();
            $output .= '</tr>';

            $results = get_users();
            foreach ($results as $index => $result) {

                $output .= '<tr>';
                $output .= '<td><a href="?view_mode=user&_id='.$results[$index]->ID.'">'.$results[$index]->display_name.'</a></td>';
                $output .= '</tr>';
            }
            $output .= '</tbody></table></figure>';

            $output .= '<form method="get">';
            $output .= '<div class="wp-block-buttons">';
            $output .= '<div class="wp-block-button">';
            //$output .= '<input class="wp-block-button__link" type="submit" value="Create" name="edit_mode">';
            $output .= '<a class="wp-block-button__link" href="/wp-admin/post-new.php?post_type=product">Create</a>';
            $output .= '</div>';
            $output .= '<div class="wp-block-button">';
            $output .= '<a class="wp-block-button__link" href="/">Cancel</a>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</form>';
            return $output;
        }
        
        function select_options( $default_id=null ) {

            $args = array(
                'post_type'      => 'product',
                'product_cat'    => 'Badges'
            );       
        
            $output = '<option value="no_select">-- Select an option --</option>';
            $loop = new WP_Query( $args );
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
            wp_reset_query();
            $output .= '<option value="delete_select">-- Remove this --</option>';

            return $output;
        }

        function select_users( $default_id=null ) {

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
            $sql = "CREATE TABLE `{$wpdb->prefix}user_badges` (
                u_b_id int NOT NULL AUTO_INCREMENT,
                student_id int NOT NULL,
                badge_id int NOT NULL,
                txid varchar(255),
                PRIMARY KEY  (u_b_id)
            ) $charset_collate;";        
            dbDelta($sql);

        }        
    }
    //if ( is_admin() )
    new badges();
}
?>