<?php

/**
 * Plugin Name: foot-reflexology
 * Plugin URI: https://wordpress.org/plugins/foot-reflexology/
 * Description: The leading web api plugin for pig system by shortcode
 * Author: dgc.network
 * Author URI: https://dgc.network/
 * Version: 1.0.0
 * Requires at least: 4.4
 * Tested up to: 5.2
 * 
 * Text Domain: foot-reflexology
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
include_once dirname( __FILE__ ) . '/includes/class-timeslots.php';
//include_once dirname( __FILE__ ) . '/includes/class-badges.php';
include_once dirname( __FILE__ ) . '/includes/class-calendars.php';
include_once dirname( __FILE__ ) . '/includes/class-orders.php';
include_once dirname( __FILE__ ) . '/includes/class-courses.php';
include_once dirname( __FILE__ ) . '/includes/class-certifications.php';
//include_once dirname( __FILE__ ) . '/includes/class-users.php';
include_once dirname( __FILE__ ) . '/vendor/autoload.php';
include_once dirname( __FILE__ ) . '/blockchain/php-OP_RETURN/OP_RETURN.php';
include_once dirname( __FILE__ ) . '/blockchain/php-OP_RETURN/op_return_setting.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/GPBMetadata/Payload.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/CreateCourseAction.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/UpdateCourseAction.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/CreateCourseLearingAction.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/UpdateCourseLearingAction.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/CreateUserCourseLearingAction.php';
include_once dirname( __FILE__ ) . '/blockchain/build/gen/UpdateUserCourseLearingAction.php';

/**
 * Register a custom menu page.
 */
function wpdocs_register_my_menu_page() {
    $menu_slug = 'wpdocs-slug';
    add_menu_page(
        __( 'Custom Menu Title', 'textdomain' ),
        __( 'Reflexology', 'textdomain' ),
        'manage_options',
        $menu_slug,
        'my_custom_menu_page',
        plugins_url( 'myplugin/images/icon.png' ),
        6
    );
}
add_action( 'admin_menu', 'wpdocs_register_my_menu_page' );

/**
 * Display a custom menu page
 */
function my_custom_menu_page(){
    //certifications::list_mode();
    esc_html_e( 'Admin Page Test', 'textdomain' );  
    //echo do_shortcode('[certification-list]');
    echo do_shortcode('[course-list]');
}

/**
 * Add product categories.
 */
wp_insert_term( 'Courses', 'product_cat', array(
    'description' => 'Description for category',
    'parent' => 0,
    'slug' => 'courses'
) );

wp_insert_term( 'Certification', 'product_cat', array(
    'description' => 'Description for category',
    'parent' => 0,
    'slug' => 'certification'
) );

wp_insert_term( 'Reservation', 'product_cat', array(
    'description' => 'Description for category',
    'parent' => 0,
    'slug' => 'reservation'
) );
/*
// Register main datepicker jQuery plugin script
add_action( 'wp_enqueue_scripts', 'enabling_date_picker' );
function enabling_date_picker() {

    // Only on front-end and checkout page
    if( is_admin() || ! is_checkout() ) return;

    // Load the datepicker jQuery-ui plugin script
    wp_enqueue_script( 'jquery-ui-datepicker' );
}
*/
/**
 * Load jQuery datepicker.
 *
 * By using the correct hook you don't need to check `is_admin()` first.
 * If jQuery hasn't already been loaded it will be when we request the
 * datepicker script.
 */
function wpse_enqueue_datepicker() {
    // Load the datepicker script (pre-registered in WordPress).
    wp_enqueue_script( 'jquery-ui-datepicker' );

    // You need styling for the datepicker. For simplicity I've linked to the jQuery UI CSS on a CDN.
    wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
    wp_enqueue_style( 'jquery-ui' );  
}
add_action( 'wp_enqueue_scripts', 'wpse_enqueue_datepicker' );

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
/*
add_filter('woocommerce_checkout_cart_item_quantity','wdm_add_user_custom_option_from_session_into_cart',1,3);  
add_filter('woocommerce_cart_item_price','wdm_add_user_custom_option_from_session_into_cart',1,3);
if(!function_exists('wdm_add_user_custom_option_from_session_into_cart')) {
    function wdm_add_user_custom_option_from_session_into_cart($product_name, $values, $cart_item_key ) {
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
                //$output .= '<dd><input name="_event_start_date" id="datepicker"></dd>';
                $output .= '<dd><input name="_event_start_date" type="date"></dd>';
                $output .= '<dd><select name="_event_start_time">'.calendars::select_time().'</select></dd>';
                $output .= "</dl>"; 
                return $output;
            }
        }
    }
}
*/
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
