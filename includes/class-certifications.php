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

                // Proceed to the WC_Order_Item to pickup the Reservation product
            }

            $user = new WP_User($_id);
            $output  = '<h2>'.$user->display_name.'的服務預約</h2>';
            $output .= '<div id="datepicker"></div>';
            $output .= '<div style="display:flex">';
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}timeslots WHERE timeslot_session = 1", OBJECT );
            $output .= '<div style="text-align:center; width:100px">';
            $output .= '<div>上午</div>';
            foreach ( $results as $index=>$result ) {
                $output .= '<div class="timepicker" style="margin:5px; border-style:solid; border-width:thin;">'.$result->timeslot_begin.'</div>';
            }
            $output .= '</div>';
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}timeslots WHERE timeslot_session = 2", OBJECT );
            $output .= '<div style="text-align:center; width:100px">';
            $output .= '<div>下午</div>';
            foreach ( $results as $index=>$result ) {
                $output .= '<div class="timepicker" style="margin:5px; border-style:solid; border-width:thin;">'.$result->timeslot_begin.'</div>';
            }
            $output .= '</div>';
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}timeslots WHERE timeslot_session = 3", OBJECT );
            $output .= '<div style="text-align:center; width:100px">';
            $output .= '<div>晚上</div>';
            foreach ( $results as $index=>$result ) {
                $output .= '<div class="timepicker" style="margin:5px; border-style:solid; border-width:thin;">'.$result->timeslot_begin.'</div>';
            }
            $output .= '</div>';
            $output .= '</div>';
            ?>
            <script>
                jQuery(document).ready(function($) {
                    $("#datepicker").datepicker({
                        onSelect: function(dateText) {
                            console.log("Selected date: " + dateText + "; input's current value: " + this.value);
                            $(this).change();
                        }
                    })
                    .on("change", function() {
                        console.log("Got change event from field");
                    });
                    //$(".timepicker").on('hover', function() {
                    //    $(".showlist-artwork,.showlist-info",this).toggle().off("hover");
                    //});
                    //$('.timepicker').css({"border-color":"gray","color":"gray"}).hover(
                    //    function(){
                    //        $(this).css({"border-color":"red","color":"red","cursor":"pointer"});
                    //    },
                    //    function(){
                    //        $(this).css({"border-color":"gray","color":"gray","cursor":"default"});
                    //    }
                    //);
                    $('.timepicker').on({
                        mouseenter: function(){
                            $(this).css({"border-color":"red","color":"red","cursor":"pointer"});
                        },
                        mouseleave: function(){
                            $(this).css({"border-color":"gray","color":"gray","cursor":"default"});
                        },
                        click: function(){
                            $(this).css({"border-color":"red","color":"red","cursor":"pointer"});
                        }
                    });
                });
            </script>
            <?php

            $output .= '<form>';
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

        function available_setting( $_id=0 ) {

            if ($_id==0){
                return '<div>ID is required</div>';
            }

            if( isset($_POST['submit_action']) ) {
                if( $_POST['submit_action']=='Submit' ) {
                    global $wpdb;
                    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}timeslots ORDER BY timeslot_begin", OBJECT );
                    foreach ( $results as $index=>$result ) {
                        if ($_POST['_available_selected_'.$index]=='true') {
                            $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}available_timeslots WHERE available_host={$_id} AND available_date={$_POST['_available_date']} AND available_time_begin={$result->timeslot_begin}", OBJECT );
                            if (empty($row)) {
                                $table = $wpdb->prefix.'available_timeslots';
                                $data = array(
                                    'available_host' => $_id,
                                    'available_date' => $_POST['_available_date'],
                                    'available_time_begin' => $result->timeslot_begin,
                                );
                                $format = array('%d', '%s', '%s');
                                $insert_id = $wpdb->insert($table, $data, $format);
                            }
                        } else {
                            $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}available_timeslots WHERE available_host={$_id} AND available_date={$_POST['_available_date']} AND available_time_begin={$result->timeslot_begin}", OBJECT );
                            if (!empty($row)) {
                                $table = $wpdb->prefix.'available_timeslots';
                                $where = array(
                                    'available_host' => $_id, 
                                    'available_date' => $_POST['_available_date'], 
                                    'available_time_begin' => $result->timeslot_begin, 
                                );
                                $deleted = $wpdb->delete( $table, $where );
                            }
                        }
                    }        
                }
                if( $_POST['submit_action']=='Cancel' ) {
                }
                unset($_GET['edit_mode']);
                unset($_POST['edit_mode']);
                return self::list_mode();

                // Proceed to the WC_Order_Item to pickup the Reservation product
            }

            $user = new WP_User($_id);
            $output  = '<h2>Available time setting</h2>';
            $output .= '<form>';
            //$output  = '<h2>'.$user->display_name.' setting</h2>';
            //$output .= '<div id="datepicker"></div>';
            //$output .= '<div style="display:flex">';
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}timeslots ORDER BY timeslot_begin", OBJECT );
            //$output .= '<div style="text-align:center; width:100px">';
            $output .= '<div><input id="datepicker" type="text" name="_available_date"></div>';
            $output .= '<div>';
            foreach ( $results as $index=>$result ) {
                $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}available_timeslots WHERE available_host={$_id} AND available_date={}", OBJECT );
                $output .= '<input type="checkbox" value="true" name="_available_selected_"'.$index;
                if (!empty($row)) {$output .= ' checked';}
                $output .= '> '.$result->timeslot_begin.' ~ '.$result->timeslot_end.'<br>';
                //$output .= '<div class="timepicker" style="margin:5px; border-style:solid; border-width:thin;">'.$result->timeslot_begin.'</div>';
            }
            $output .= '</div>';
            //$output .= '</div>';
            ?>
            <script>
                jQuery(document).ready(function($) {
                    $("#datepicker").datepicker({
                        onSelect: function(dateText) {
                            $("input[name='_available_date']").val() = this.value;
                            console.log("Selected date: " + dateText + "; input's current value: " + this.value);
                            $(this).change();
                        }
                    })
                    .on("change", function() {
                        console.log("Got change event from field");
                    });
                    //$(".timepicker").on('hover', function() {
                    //    $(".showlist-artwork,.showlist-info",this).toggle().off("hover");
                    //});
                    //$('.timepicker').css({"border-color":"gray","color":"gray"}).hover(
                    //    function(){
                    //        $(this).css({"border-color":"red","color":"red","cursor":"pointer"});
                    //    },
                    //    function(){
                    //        $(this).css({"border-color":"gray","color":"gray","cursor":"default"});
                    //    }
                    //);
                    $('.timepicker').on({
                        mouseenter: function(){
                            $(this).css({"border-color":"red","color":"red","cursor":"pointer"});
                        },
                        mouseleave: function(){
                            $(this).css({"border-color":"gray","color":"gray","cursor":"default"});
                        },
                        click: function(){
                            $(this).css({"border-color":"red","color":"red","cursor":"pointer"});
                        }
                    });
                });
            </script>
            <?php

            $output .= '<div class="wp-block-buttons">';
            $output .= '<div class="wp-block-button">';
            $output .= '<input class="wp-block-button__link" type="submit" value="Submit" name="submit_action">';
            $output .= '</div>';
            $output .= '<div class="wp-block-button">';
            $output .= '<input class="wp-block-button__link" type="submit" value="Cancel" name="submit_action">';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</form>';

            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}available_timeslots", OBJECT );
            $output .=  var_dump($results);
            return $output;
        }

        static function list_mode() {
            
            if( isset($_GET['view_mode']) ) {
                if ($_GET['view_mode']=='Available') return self::available_setting($_GET['_id']);
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
                $output .= '<div><h2><a href="?view_mode=Available&_id='.$order->get_user_id().'">'.$user->display_name.'</a></h2></div>';
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

            $sql = "CREATE TABLE `{$wpdb->prefix}available_timeslots` (
                available_time_id int NOT NULL AUTO_INCREMENT,
                available_host int NOT NULL,
                available_date varchar(10) NOT NULL,
                available_time_begin varchar(10) NOT NULL,
                PRIMARY KEY  (available_time_id)
            ) $charset_collate;";        
            dbDelta($sql);

        }        
    }
    new certifications();
}
?>