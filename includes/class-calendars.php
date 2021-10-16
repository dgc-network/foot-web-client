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
            //return $output;

            /** 
             * user course relationship with learning
             */
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_course_learnings WHERE student_id = {$current_user_id} AND course_id = {$_id}", OBJECT );
            if (empty($results)) {
                $c_results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE course_id = {$_id}", OBJECT );
                foreach ($c_results as $index => $result) {
                    $table = $wpdb->prefix.'user_course_learnings';
                    $data = array(
                        'student_id' => $current_user_id,
                        'learning_id' => $c_results[$index]->learning_id,
                        'course_id' => $_id,
                        //'lecturer_id' => $_POST['_lecturer_id'],
                        //'witness_id' => $_POST['_witness_id'],
                    );
                    //$format = array('%d', '%d', '%d', '%d', '%d');
                    $format = array('%d', '%d', '%d');
                    $wpdb->insert($table, $data, $format);
                }
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_course_learnings WHERE student_id = {$current_user_id} AND course_id = {$_id}", OBJECT );
            }
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>'.'#'.'</td><td>Learnings</td><td>Lecturers</td><td>Witnesses</td></tr>';
            foreach ($results as $index => $result) {
                $output .= '<tr><td>'.($index+1).'</td>';
                //$output .= '<td>'.'<select name="_learning_id_'.$index.'">'.courses::select_learnings($_id, $results[$index]->learning_id).'</select></td>';
                $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE learning_id = {$results[$index]->learning_id}", OBJECT );
                $output .= '<td><a href="'.$row->learning_link.'">'.$row->learning_title.'</a></td>';
                $output .= '<td>'.'<select name="_lecturer_id_'.$index.'">'.courses::select_lecturers($results[$index]->learning_id, $results[$index]->lecturer_id).'</select></td>';
                $output .= '<td>'.'<select name="_witness_id_'.$index.'">'.courses::select_witnesses($results[$index]->learning_id, $results[$index]->witness_id).'</select></td>';
                $output .= '</tr>';
            }
/*            
            $output .= '<tr><td>'.'#'.'</td>';
            $output .= '<td>'.'<select name="_learning_id">'.courses::select_learnings($_id).'</select>'.'</td>';
            $output .= '<td></td><td></td>';
*/            
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

        function view_mode($_id=null) {

            if ($_id==null){
                $_id=get_current_order_id();
            }

            if( isset($_POST['submit_action']) ) {
        
                global $wpdb;
                /** 
                 * submit the order relationship with course learning
                 */
                $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}course_learnings WHERE course_id = {$_id}", OBJECT );
                foreach ($results as $index => $result) {

                    $op_result = OP_RETURN_send(OP_RETURN_SEND_ADDRESS, OP_RETURN_SEND_AMOUNT, $send_data);
                    //return var_dump($op_result);
                
                    if (isset($op_result['error'])) {

                        $result_output = 'Error: '.$op_result['error']."\n";
                        return $result_output;
                    }
                    else {
                        //$result_output = 'TxID: '.$op_result['txid']."\nWait a few seconds then check on: http://coinsecrets.org/\n";

                        global $wpdb;
                        $table = $wpdb->prefix.'order_course_learnings';
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
            $output .= '<tr><td>'.'Name:'.'</td><td>'.get_orderdata($_id)->display_name.'</td></tr>';
            $output .= '<tr><td>'.'Email:'.'</td><td>'.get_orderdata($_id)->order_email.'</td></tr>';
            $output .= '</tbody></table></figure>';
            //return $output;

            /** 
             * order relationship with course learnings
             */
            $course_header = true;
            $output .= '<figure class="wp-block-table"><table><tbody>';
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}order_course_learnings WHERE student_id = {$_id} ORDER BY course_id", OBJECT );
            foreach ($results as $index => $result) {
                if ($course_id == $results[$index]->course_id){$course_header=false;}
                if ($course_header) {
                    $course_id = $results[$index]->course_id;
                    //$row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}courses WHERE course_id = {$course_id}", OBJECT );
                    //$output .= '<tr><td colspan="4">'.$row->course_title.'</td></td>';
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
                $output .= '<td>'.get_orderdata($results[$index]->lecturer_id)->display_name.'</td>';
                $output .= '<td><input type="text" name="_lecture_date_'.$index.'" value="'.$lectureDate.'">'.'</td>';
                $output .= '<td>'.get_orderdata($results[$index]->witness_id)->display_name.'</td>';
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

        function edit_mode( $_id=null, $_mode ) {

            if ($_id==null){
                $_id=get_current_order_id();
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
                $output .= '<tr><td>'.'Name:'.'</td><td><input style="width: 100%" type="text" name="_display_name" value="'.get_orderdata($_id)->display_name.'"></td></tr>';
                $output .= '<tr><td>'.'Email:'.'</td><td><input style="width: 100%" type="text" name="_order_email" value="'.get_orderdata($_id)->order_email.'"></td></tr>';
            } else if( $_mode=='Delete' ) {
                $output .= '<tr><td>'.'Name:'.'</td><td><input style="width: 100%" type="text" name="_display_name" value="'.get_orderdata($_id)->display_name.'" disabled></td></tr>';
                $output .= '<tr><td>'.'Email:'.'</td><td><input style="width: 100%" type="text" name="_order_email" value="'.get_orderdata($_id)->order_email.'" disabled></td></tr>';
            } else {
                $output .= '<tr><td>'.'Name:'.'</td><td><input style="width: 100%" type="text" name="_display_name" value=""></td></tr>';
                $output .= '<tr><td>'.'Email:'.'</td><td><input style="width: 100%" type="text" name="_order_email" value=""></td></tr>';
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
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}my_calendars WHERE event_host = {$user_id}", OBJECT );
            $output  = '<h2>My Calendars</h2>';
            $output .= '<figure class="wp-block-table"><table><tbody>';
            $output .= '<tr><td>Title</td><td>Begin</td><td>End</td><td></td></tr>';
            foreach ( $results as $index=>$result ) {
                //$output .= '<form method="post">';
                $output .= '<tr>';
                $output .= '<td><a href="?view_mode=true&_id='.$result->event_id.'">'.$result->event_title.'</a></td>';
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
            $output .= '<input class="wp-block-button__link" type="submit" value="Create" name="edit_mode">';
            //$output .= '<a class="wp-block-button__link" href="/checkout">Create</a>';
            $output .= '</div>';
            $output .= '<div class="wp-block-button">';
            $output .= '<a class="wp-block-button__link" href="/">Cancel</a>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</form>';

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

            $sql = "CREATE TABLE `{$wpdb->prefix}my_calendars` (
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
function add_product_to_cart() {
	if ( ! is_admin() ) {
		$product_id = 295; //replace with your own product id
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

// Register main datepicker jQuery plugin script
add_action( 'wp_enqueue_scripts', 'enabling_date_picker' );
function enabling_date_picker() {

    // Only on front-end and checkout page
    if( is_admin() || ! is_checkout() ) return;

    // Load the datepicker jQuery-ui plugin script
    wp_enqueue_script( 'jquery-ui-datepicker' );
}

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

    woocommerce_form_field(
        'order_pickup_date', 
        array(
            'type'          => 'text',
            'class'         => array('my-field-class form-row-wide'),
            'id'            => 'datepicker',
            'required'      => true,
            'label'         => __('Delivery Date'),
            'placeholder'   => __('Select Date'),
            'options'       => $mydateoptions
        ), 
        $checkout->get_value( 'order_pickup_date' )
    );

    $technician_options = array('roverchen','李光祥');
    woocommerce_form_field(
        'pickup_technician', 
        array(
            'type'          => 'text',
            'class'         => array('my-field-class form-row-wide'),
            'id'            => 'technicianpicker',
            'required'      => true,
            'label'         => __('Technician'),
            'placeholder'   => __('Select Technician'),
            'options'       => $technician_options
        ), 
        $checkout->get_value( 'pickup_technician' )
    );

    echo '</div>';
}
?>