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
            add_shortcode('certification-list', __CLASS__ . '::list_mode');
            self::create_tables();
        }

        function booking( $_id=0 ) {

            if ($_id==0){
                return '<div>ID is required</div>';
            }

            if( isset($_POST['submit_action']) ) {
                if( $_POST['submit_action']=='Cancel' ) {
                    unset($_GET['edit_mode']);
                    unset($_POST['edit_mode']);
                    return self::list_mode();
                }

                // Proceed the WC_Order_Item to pickup the Reservation product
            }

            $user = new WP_User($_id);
            $output  = '<h2>'.$user->display_name.'線上預約</h2>';
            $output .= '<div id="datepicker"></div>';
            $output .= '<div style="display:flex">';
            $output .= '<div style="text-align:center; width:100px">';
            $output .= '<div>上午</div>';
            $output .= '<div style="margin:5px; border-style:solid; border-width:thin;">08:00</div>';
            $output .= '<div style="margin:5px; border-style:solid; border-width:thin;">09:00</div>';
            $output .= '<div style="margin:5px; border-style:solid; border-width:thin;">10:00</div>';
            $output .= '<div style="margin:5px; border-style:solid; border-width:thin;">11:00</div>';
            $output .= '</div>';
            $output .= '<div style="text-align:center; width:100px">';
            $output .= '<div>下午</div>';
            $output .= '<div style="margin:5px; border-style:solid; border-width:thin;">13:00</div>';
            $output .= '<div style="margin:5px; border-style:solid; border-width:thin;">14:00</div>';
            $output .= '<div style="margin:5px; border-style:solid; border-width:thin;">15:00</div>';
            $output .= '<div style="margin:5px; border-style:solid; border-width:thin;">16:00</div>';
            $output .= '</div>';
            $output .= '<div style="text-align:center; width:100px">';
            $output .= '<div>晚上</div>';
            $output .= '<div style="margin:5px; border-style:solid; border-width:thin;">18:00</div>';
            $output .= '<div style="margin:5px; border-style:solid; border-width:thin;">19:00</div>';
            $output .= '<div style="margin:5px; border-style:solid; border-width:thin;">20:00</div>';
            $output .= '<div style="margin:5px; border-style:solid; border-width:thin;">21:00</div>';
            $output .= '</div>';
            $output .= '</div>';
            ?>
            <script>
                jQuery(document).ready(function($) {
                    //$("#datepicker").datepicker();
                    $("#datepicker").datepicker({
                        onSelect: function(dateText) {
                            console.log("Selected date: " + dateText + "; input's current value: " + this.value);
                            $(this).change();
                        }
                    })
                    .on("change", function() {
                        console.log("Got change event from field");
                    });
                });
            </script>
            <?php

            /** 
             * footer
             */
            $output .= '<div class="wp-block-buttons">';
            $output .= '<div class="wp-block-button">';
            $output .= '<input class="wp-block-button__link" type="submit" value="Submit" name="submit_action">';
            $output .= '</div>';
            $output .= '<div class="wp-block-button">';
            $output .= '<input class="wp-block-button__link" type="submit" value="Cancel" name="submit_action">';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</form>';

            return $output;
        }

        static function list_mode() {
            
            if( isset($_GET['view_mode']) ) {
                if ($_GET['view_mode']=='Booking') return self::booking($_GET['_id']);
                if ($_GET['view_mode']=='More...') return self::see_more($_GET['_id']);
            }

            /**
             * List Mode
             */
            $args = array(
                'post_type'      => 'product',
                'product_cat'    => 'Certification',
                'posts_per_page' => 100,
                'order'          => 'ASC'
            );
            
            $customer_orders = [];
            foreach ( wc_get_is_paid_statuses() as $paid_status ) {
                $customer_orders += wc_get_orders( [
                    'type'        => 'shop_order',
                    'limit'       => - 1,
                    'status'      => $paid_status,
                ] );
            }
            $order_items = [];
            $loop = new WP_Query( $args );
            while ( $loop->have_posts() ) : $loop->the_post();
                global $product;
                foreach ( $customer_orders as $order ) {
                    foreach ( $order->get_items() as $item ) {
                        $item_product = $item->get_product();
                        if ($item_product->get_id()==$product->get_id()){
                            array_push($order_items, $item);
                        }
                    }
                }
            endwhile;
            wp_reset_query();

            $output  = '<h2>認證列表</h2>';
            $output .= '<div style="display:flex">';
            foreach ( $order_items as $item ) {
                $order = $item->get_order();
                $product = $item->get_product();
                $user = $order->get_user();

                $output .= '<div style="display:flex">';
                $output .= '<div style="">';
                $output .= '<img src="'.get_avatar_url($order->get_customer_id()).'">';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<div><h1>'.$user->display_name.'</h1></div>';
                $output .= '<div>'.$item->get_name().'</div>';
                $output .= '<form method="get">';
                $output .= '<div class="wp-block-buttons">';
                $output .= '<div class="wp-block-button">';
                $output .= '<input class="wp-block-button__link" type="submit" value="Booking" name="view_mode">';
                $output .= '</div>';
                $output .= '<div class="wp-block-button">';
                $output .= '<input class="wp-block-button__link" type="submit" value="More..." name="view_mode">';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<input type="hidden" value="'.$order->get_user_id().'" name="_id">';
                $output .= '</form>';
                $output .= '</div>';
                $output .= '</div>';
            }
            $output .= '</div>';
            return $output;
        }
        
        function select_options( $default_id=null ) {

            $args = array(
                'post_type'      => 'product',
                'product_cat'    => 'Courses',
                'posts_per_page' => 100,
                'order'         => 'ASC'
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
                learning_id int NOT NULL,
                course_id int NOT NULL,
                lecturer_id int,
                lecture_date int,
                witness_id int,
                certified_date int,
                txid varchar(255),
                is_deleted boolean,
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
    new certifications();
}
?>