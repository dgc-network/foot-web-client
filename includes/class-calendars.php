<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('calendars')) {

    class calendars {

        /**
         * Class constructor
         */
        public function __construct() {
            add_shortcode('calendar_list', __CLASS__ . '::list_mode');
            add_shortcode('calendar-list', __CLASS__ . '::list_mode');
            add_shortcode('calendar_edit', __CLASS__ . '::edit_mode');
            add_shortcode('calendar_view', __CLASS__ . '::view_mode');
            self::create_tables();
            wp_insert_term( 'Reservation', 'product_cat', array(
                'description' => 'Description for category', // optional
                'parent' => 0, // optional
                'slug' => 'reservation' // optional
            ) );
        }

        function edit_mode( $_id=null, $_mode ) {

            if ($_id==null){
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
            global $wpdb;
            $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}calendars WHERE event_id = {$_id}", OBJECT );
            $output  = '<form method="post">';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'Title:'.'</td><td><input style="width: 100%" type="text" name="_event_title" value="'.$row->event_title.'"></td></tr>';
            $output .= '<tr><td>'.'Begin:'.'</td><td><input style="width: 100%" type="text" name="_event_begin" value="'.$row->event_begin.'"></td></tr>';
            $output .= '<tr><td>'.'End:'.'</td><td><input style="width: 100%" type="text" name="_event_end" value="'.$row->event_end.'"></td></tr>';
/*
            if( $_mode=='Update' ) {
                $output .= '<tr><td>'.'Name:'.'</td><td><input style="width: 100%" type="text" name="_display_name" value="'.get_orderdata($_id)->display_name.'"></td></tr>';
                $output .= '<tr><td>'.'Email:'.'</td><td><input style="width: 100%" type="text" name="_order_email" value="'.get_orderdata($_id)->order_email.'"></td></tr>';
            } else if( $_mode=='Delete' ) {
                $output .= '<tr><td>'.'Name:'.'</td><td><input style="width: 100%" type="text" name="_display_name" value="'.get_orderdata($_id)->display_name.'" disabled></td></tr>';
                $output .= '<tr><td>'.'Email:'.'</td><td><input style="width: 100%" type="text" name="_order_email" value="'.get_orderdata($_id)->order_email.'" disabled></td></tr>';
            } else {
                $output .= '<tr><td>'.'Name:'.'</td><td><input style="width: 100%" type="text" name="_display_name" value=""></td></tr>';
                $output .= '<tr><td>'.'Email:'.'</td><td><input style="width: 100%" type="text" name="_order_email" value=""></td></tr>';
            }
*/            
            $output .= '</tbody></table></figure>';
    
            $output .= '<div class="wp-block-buttons">';
            $output .= '<div class="wp-block-button">';
            $output .= '<input class="wp-block-button__link" type="submit" value="Update" name="update_action">';
            $output .= '</div>';
            $output .= '<div class="wp-block-button">';
            $output .= '<input class="wp-block-button__link" type="submit" value="Delete" name="delete_action">';
/*            
            if( $_mode=='Update' ) {
                //$output .= '<input class="wp-block-button__link" type="submit" value="Update" name="update_action">';
            } else if( $_mode=='Delete' ) {
                //$output .= '<input class="wp-block-button__link" type="submit" value="Delete" name="delete_action">';
            } else {
                //$output .= '<input class="wp-block-button__link" type="submit" value="Create" name="create_action">';
            }
*/            
            $output .= '</div>';
/*            
            $output .= '<div class="wp-block-button">';
            $output .= '<input class="wp-block-button__link" type="submit" value="Cancel"';
            $output .= '</div>';
*/            
            $output .= '</div>';
            $output .= '</form>';
        
            return $output;
        }

        function list_mode() {

            if( isset($_GET['view_mode']) ) {
                if ($_GET['view_mode']=='course_learnings') return self::course_learnings($_GET['_id']);
                return self::view_mode($_GET['_id']);
            }

            if( isset($_GET['edit_mode']) ) {
                if ($_GET['edit_mode']=='Create') {
                    add_product_to_cart();
                    ?><script>window.location='/checkout'</script><?php
                }
                return self::edit_mode($_POST['_id'], $_POST['edit_mode']);
            }            

            /**
             * List Mode
             */
            global $wpdb;
            $user_id = get_current_user_id();
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}calendars WHERE event_host = {$user_id}", OBJECT );
            $output  = '<h2>My Calendars</h2>';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>Title</td><td>Begin</td><td>End</td><td></td></tr>';
            foreach ( $results as $index=>$result ) {
                //$output .= '<form method="post">';
                $output .= '<tr>';
                $output .= '<td><a href="?edit_mode=true&_id='.$result->event_id.'">'.$result->event_title.'</a></td>';
                $output .= '<td>'.$result->event_begin.'</td>';
                $output .= '<td>'.$result->event_end.'</td>';
                //$output .= '<input type="hidden" value="'.$product->get_id().'" name="_id">';
                $output .= '</tr>';
                //$output .= '</form>';

            }
            $output .= '</tbody></table></figure>';

            $output .= '<form method="get">';
            $output .= '<div class="wp-block-buttons">';
            $output .= '<div class="wp-block-button">';
            //$output .= '<input class="wp-block-button__link" type="submit" value="Create" name="edit_mode">';
            //$output .= '<a class="wp-block-button__link" href="/checkout">Create</a>';
            $output .= '</div>';
            $output .= '<div class="wp-block-button">';
            $output .= '<a class="wp-block-button__link" href="/">Cancel</a>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</form>';

            return $output;
        }
        
        function select_time() {
            $output  = '<option value="no_select">-- Select a time --</option>';
            $output .= '<option value="08000900">08:00-09:00</option>';
            $output .= '<option value="09001000">09:00-10:00</option>';
            $output .= '<option value="10001100">10:00-11:00</option>';
            $output .= '<option value="11001200">11:00-12:00</option>';
            return $output;
        }
        
        function select_options( $default_id=null ) {

            $results = get_orders();
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

            $sql = "CREATE TABLE `{$wpdb->prefix}calendars` (
                event_id int NOT NULL AUTO_INCREMENT,
                event_begin int NOT NULL,
                event_end int,
                event_title varchar(255),
                event_auther int,
                event_hos int,
                txid varchar(255),
                is_deleted boolean,
                PRIMARY KEY  (event_id)
            ) $charset_collate;";        
            dbDelta($sql);

        }
        
    }
    //if ( is_admin() )
    new calendars();
}

/**
 * Automatically add product to cart on visit
 */
add_action( 'template_redirect', 'add_product_to_cart' );
function add_product_to_cart($product_id = 295) {
	if ( ! is_admin() ) {
		//$product_id = 295; //replace with your own product id
		$found = false;
		//check if product already in cart
		if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				$_product = $values['data'];
				if ( $_product->get_id() == $product_id )
					$found = true;
			}
			// if product not found, add it
			if ( ! $found )
				WC()->cart->add_to_cart( $product_id );
		} else {
			// if no products in cart, add it
			WC()->cart->add_to_cart( $product_id );
		}
	}
}
/*
add_filter('woocommerce_add_cart_item_data','wdm_add_item_data',1,2);
if(!function_exists('wdm_add_item_data'))
{
    function wdm_add_item_data($cart_item_data,$product_id)
    {
        // Here, We are adding item in WooCommerce session with, wdm_user_custom_data_value name
        global $woocommerce;
        session_start();    
        if (isset($_SESSION['wdm_user_custom_data'])) {
            $option = $_SESSION['wdm_user_custom_data'];       
            $new_value = array('wdm_user_custom_data_value' => $option);
        }
        if(empty($option))
            return $cart_item_data;
        else
        {    
            if(empty($cart_item_data))
                return $new_value;
            else
                return array_merge($cart_item_data,$new_value);
        }
        unset($_SESSION['wdm_user_custom_data']); 
        //Unset our custom session variable, as it is no longer needed.
    }
}

add_filter('woocommerce_get_cart_item_from_session', 'wdm_get_cart_items_from_session', 1, 3 );
if(!function_exists('wdm_get_cart_items_from_session'))
{
    function wdm_get_cart_items_from_session($item,$values,$key)
    {
        if (array_key_exists( 'wdm_user_custom_data_value', $values ) )
        {
        $item['wdm_user_custom_data_value'] = $values['wdm_user_custom_data_value'];
        }       
        return $item;
    }
}
*/
add_filter('woocommerce_checkout_cart_item_quantity','wdm_add_user_custom_option_from_session_into_cart',1,3);  
add_filter('woocommerce_cart_item_price','wdm_add_user_custom_option_from_session_into_cart',1,3);
if(!function_exists('wdm_add_user_custom_option_from_session_into_cart'))
{
 function wdm_add_user_custom_option_from_session_into_cart($product_name, $values, $cart_item_key )
    {
        echo '
        <script>
            jQuery(function($){
                $("#datepicker").datepicker();
            });
        </script>';

        //$cart = WC()->cart;
        $cart = WC()->cart->get_cart();
        $cart_item = $cart[$cart_item_key];
        $product_id = $cart[$cart_item_key]['product_id'];
        $terms = get_the_terms( $product_id, 'product_cat' );
        foreach ($terms as $term) {
            //return $product_cat = $term->name;
            if ($term->name=='Reservation'){
                $output = $product_name . "</a><dl class='variation'>";
                $learning_id=1;
                $output .= '<dd><select name="_event_host">'.certifications::select_options($learning_id).'</select></dd>';
                $output .= '<dd><input name="_event_start_date" id="datepicker"></dd>';
                $output .= '<dd><select name="_event_start_time">'.calendars::select_time().'</select></dd>';
                $output .= "</dl>"; 
                return $output;
            }
        }
    }
}

// Register main datepicker jQuery plugin script
add_action( 'wp_enqueue_scripts', 'enabling_date_picker' );
function enabling_date_picker() {

    // Only on front-end and checkout page
    if( is_admin() || ! is_checkout() ) return;

    // Load the datepicker jQuery-ui plugin script
    wp_enqueue_script( 'jquery-ui-datepicker' );
}
/*
// Call datepicker functionality in your custom text field
add_action('woocommerce_after_order_notes', 'my_custom_checkout_field', 10, 1);
function my_custom_checkout_field( $checkout ) {

    date_default_timezone_set('America/Los_Angeles');
    $mydateoptions = array('' => __('Select PickupDate', 'woocommerce' ));

    echo '<div id="my_custom_checkout_field">
    <h3>'.__('Delivery Info').'</h3>';

    // YOUR SCRIPT HERE BELOW 
    echo '
    <script>
        jQuery(function($){
            $("#datepicker").datepicker();
        });
    </script>';

    $technician_options = array('roverchen','李光祥');
    woocommerce_form_field(
        'pickup_technician', 
        array(
            'type'          => 'select',
            'class'         => array('my-field-class form-row-wide'),
            'id'            => 'technicianpicker',
            'required'      => true,
            'label'         => __('Technician'),
            'placeholder'   => __('Select Technician'),
            'options'       => technician_options(),
            //'default'       => 'N'
        ),
        $checkout->get_value( 'pickup_technician' )
    );

    echo '</div>';
}

function technician_options( $learning_id=null ) {

    $technician_options = array('' => __('Select Technician', 'woocommerce' ));
    if ($learning_id==null){
        return $technician_options;
    }
    global $wpdb;
    $c_results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE teaching_id = {$learning_id}", OBJECT );
    foreach ($c_results as $c_index => $result) {
        $u_results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_course_learnings WHERE learning_id = {$c_results[$c_index]->learning_id} ORDER BY student_id", OBJECT );
        $first_line=true;
        foreach ($u_results as $u_index => $result) {
            if ($student_id==$u_results[$u_index]->student_id) $first_line=false;
            if ($first_line) {
                //$output .= '<tr><td><li><a href="?view_mode=true&_id='.$u_results[$u_index]->student_id.'">'.get_userdata($u_results[$u_index]->student_id)->display_name.'</a></td></tr>';
                //if ( $product->get_id() == $default_id ) {
                //    $output .= '<option value="'.$u_results[$u_index]->student_id.'" selected>';
                //} else {
                //    $output .= '<option value="'.$u_results[$u_index]->student_id.'">';
                //}
                //$output .= get_userdata($u_results[$u_index]->student_id)->display_name;
                //$output .= '</option>';
                //array_push($technician_options,($u_results[$u_index]->student_id=>(get_userdata($u_results[$u_index]->student_id)->display_name)));
                $$technician_options[$u_results[$u_index]->student_id] = get_userdata($u_results[$u_index]->student_id)->display_name;
                $student_id=$u_results[$u_index]->student_id;
            }
        }
    }
    return $technician_options;
}
*/
?>