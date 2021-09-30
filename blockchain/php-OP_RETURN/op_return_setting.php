<?php

if( isset($_POST['submit']) ) {
    $options = get_option( 'op_return_settings' );
    return $options['ip_address_field'];
	//$op_options = get_option( 'op_return_settings' );
	//$op_options = get_option( 'ip_address_field' );
    //return $op_options;
    //echo $my_options['title'];
    //echo $my_options['id_number'];
    //define('OP_RETURN_BITCOIN_IP', '127.0.0.1'); // IP address of your bitcoin node
	//define('OP_RETURN_BITCOIN_IP', '192.192.155.52'); // IP address of your bitcoin node
	define('OP_RETURN_BITCOIN_IP', '114.32.252.82'); // IP address of your bitcoin node
	//define('OP_RETURN_BITCOIN_IP', '218.161.56.168'); // IP address of your bitcoin node
	define('OP_RETURN_BITCOIN_USE_CMD', false); // use command-line instead of JSON-RPC?
	
	if (OP_RETURN_BITCOIN_USE_CMD) {
		define('OP_RETURN_BITCOIN_PATH', '/usr/bin/bitcoin-cli'); // path to bitcoin-cli executable on this server

	} else {
		//define('OP_RETURN_BITCOIN_PORT', ''); // leave empty to use default port for mainnet/testnet
		//define('OP_RETURN_BITCOIN_USER', ''); // leave empty to read from ~/.bitcoin/bitcoin.conf (Unix only)
		//define('OP_RETURN_BITCOIN_PASSWORD', ''); // leave empty to read from ~/.bitcoin/bitcoin.conf (Unix only)
		define('OP_RETURN_BITCOIN_PORT', '7998'); // leave empty to use default port for mainnet/testnet
		define('OP_RETURN_BITCOIN_USER', 'digitalcoinrpc'); // leave empty to read from ~/.bitcoin/bitcoin.conf (Unix only)
		define('OP_RETURN_BITCOIN_PASSWORD', '56c735f3910a53eeda0357670bc6a02f'); // leave empty to read from ~/.bitcoin/bitcoin.conf (Unix only)

	}
	
	//define('OP_RETURN_BTC_FEE', 0.0001); // BTC fee to pay per transaction
	//define('OP_RETURN_BTC_DUST', 0.00001); // omit BTC outputs smaller than this

	define('OP_RETURN_SEND_AMOUNT', 0.00000002); // BTC send amount per transaction
	define('OP_RETURN_SEND_ADDRESS', 'DTZfSbVQnBs2YnsHpyuuZ1Mv3cJBhgav66'); // BTC send address per transaction

	define('OP_RETURN_BTC_FEE', 0.00000004); // BTC fee to pay per transaction
	define('OP_RETURN_BTC_DUST', 0); // omit BTC outputs smaller than this

	define('OP_RETURN_MAX_BYTES', 80); // maximum bytes in an OP_RETURN (80 as of Bitcoin 0.11)
	define('OP_RETURN_MAX_BLOCKS', 10); // maximum number of blocks to try when retrieving data

	define('OP_RETURN_NET_TIMEOUT_CONNECT', 5); // how long to time out when connecting to bitcoin node
	define('OP_RETURN_NET_TIMEOUT_RECEIVE', 10); // how long to time out retrieving data from bitcoin node
}
//
function op_return_add_settings_page() {
    add_options_page(
      'OP_RETURN Settings',
      'OP_RETURN',
      'manage_options',
      'op-return-page',
      'op_return_render_settings_page'
    );
}
add_action( 'admin_menu', 'op_return_add_settings_page' );

//
function op_return_render_settings_page() {
?>
    <h2>OP_RETURN Settings</h2>
    <form action="options.php" method="post">
        <?php 
        //settings_fields( 'my_options_group' );
        settings_fields( 'op_return_group' );
        do_settings_sections( 'op_return_page' );
        ?>
        <input
           type="submit"
           name="submit"
           class="button button-primary"
           value="<?php esc_attr_e( 'Save' ); ?>"
        />
    </form>
<?php
}

/**
* Registers a text field setting for Wordpress 4.7 and higher.
**/
function register_my_setting() {
    $args = array(
            'type' => 'string', 
            'sanitize_callback' => 'sanitize_text_field',
            'default' => NULL,
            );
    register_setting( 'my_options_group', 'my_option_name', $args ); 
} 
//add_action( 'admin_init', 'register_my_setting' );

function op_return_register_settings() {
    register_setting(
        'op_return_group',
        'op_return_settings',
        'op_return_sanitize_callback'
    );

    add_settings_section(
        'section_one',
        'Digitalcoin Configuration',
        'op_return_section_one_callback',
        'op_return_page'
    );

    add_settings_field(
        'ip_address_field',
        'IP Address:',
        'op_return_render_ip_address_field',
        'op_return_page',
        'section_one'
    );

    add_settings_field(
        'port_number_field',
        'Port Number:',
        'op_return_render_port_number_field',
        'op_return_page',
        'section_one'
    );

    add_settings_field(
        'rpc_user_field',
        'RPC User:',
        'op_return_render_rpc_user_field',
        'op_return_page',
        'section_one'
    );

    add_settings_field(
        'rpc_password_field',
        'RPC Password:',
        'op_return_render_rpc_password_field',
        'op_return_page',
        'section_one'
    );

    add_settings_field(
        'send_amount_field',
        'Send Amount:',
        'op_return_render_send_amount_field',
        'op_return_page',
        'section_one'
    );

    add_settings_field(
        'send_address_field',
        'Send Address:',
        'op_return_render_send_address_field',
        'op_return_page',
        'section_one'
    );

    add_settings_field(
        'transaction_fee_field',
        'Transaction Fee:',
        'op_return_render_transaction_fee_field',
        'op_return_page',
        'section_one'
    );

    add_settings_field(
        'dust_amount_field',
        'Dust Amount:',
        'op_return_render_dust_amount_field',
        'op_return_page',
        'section_one'
    );

    add_settings_field(
        'max_bytes_field',
        'Max Bytes:',
        'op_return_render_max_bytes_field',
        'op_return_page',
        'section_one'
    );

    add_settings_field(
        'max_blocks_field',
        'Max Blocks:',
        'op_return_render_max_blocks_field',
        'op_return_page',
        'section_one'
    );

    add_settings_field(
        'connect_timeout_field',
        'Connect Timeout:',
        'op_return_render_connect_timeout_field',
        'op_return_page',
        'section_one'
    );

    add_settings_field(
        'receive_timeout_field',
        'Receive Timeout:',
        'op_return_render_receive_timeout_field',
        'op_return_page',
        'section_one'
    );

}
add_action( 'admin_init', 'op_return_register_settings' );

function op_return_sanitize_callback( $input ) {
    $output['ip_address_field']      = sanitize_text_field( $input['ip_address_field'] );
    $output['port_number_field']     = sanitize_text_field( $input['port_number_field'] );
    $output['rpc_user_field']        = sanitize_text_field( $input['rpc_user_field'] );
    $output['rpc_password_field']    = sanitize_text_field( $input['rpc_password_field'] );
    $output['send_amount_field']     = floatval( $input['send_amount_field'] );
    $output['send_address_field']    = sanitize_text_field( $input['send_address_field'] );
    $output['transaction_fee_field'] = (float)$input['transaction_fee_field'];
    $output['dust_amount_field']     = (float)$input['dust_amount_field'];
    $output['max_bytes_field']       = (int)$input['max_bytes_field'];
    $output['max_blocks_field']      = (int)$input['max_blocks_field'];
    $output['connect_timeout_field'] = (int)$input['connect_timeout_field'];
    $output['receive_timeout_field'] = (int)$input['receive_timeout_field'];
    // ...
    return $output;
}

function op_return_section_one_callback() {
    //echo '<p>This is the first (and only) section in my settings.</p>';
}
  
function op_return_render_ip_address_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="text" size="50"  name="%s" value="%s" />',
      esc_attr( 'op_return_settings[ip_address_field]' ),
      esc_attr( $options['ip_address_field'] )
    );
}

function op_return_render_port_number_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="text" size="50" name="%s" value="%s" />',
      esc_attr( 'op_return_settings[port_number_field]' ),
      esc_attr( $options['port_number_field'] )
    );
}

function op_return_render_rpc_user_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="text" size="50" name="%s" value="%s" />',
      esc_attr( 'op_return_settings[rpc_user_field]' ),
      esc_attr( $options['rpc_user_field'] )
    );
}

function op_return_render_rpc_password_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="text" size="50" name="%s" value="%s" />',
      esc_attr( 'op_return_settings[rpc_password_field]' ),
      esc_attr( $options['rpc_password_field'] )
    );
}

function op_return_render_send_amount_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="number" size="50" name="%s" value="%s" />',
      esc_attr( 'op_return_settings[send_amount_field]' ),
      esc_attr( $options['send_amount_field'] )
    );
}

function op_return_render_send_address_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="text" size="50" name="%s" value="%s" />',
      esc_attr( 'op_return_settings[send_address_field]' ),
      esc_attr( $options['send_address_field'] )
    );
}

function op_return_render_transaction_fee_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="number" size="50" name="%s" value="%s" />',
      esc_attr( 'op_return_settings[transaction_fee_field]' ),
      esc_attr( $options['transaction_fee_field'] )
    );
}

function op_return_render_dust_amount_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="number" size="50" name="%s" value="%s" />',
      esc_attr( 'op_return_settings[dust_amount_field]' ),
      esc_attr( $options['dust_amount_field'] )
    );
}

function op_return_render_max_bytes_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="number" size="50" name="%s" value="%s" />',
      esc_attr( 'op_return_settings[max_bytes_field]' ),
      esc_attr( $options['max_bytes_field'] )
    );
}

function op_return_render_max_blocks_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="number" size="50" name="%s" value="%s" />',
      esc_attr( 'op_return_settings[max_blocks_field]' ),
      esc_attr( $options['max_blocks_field'] )
    );
}

function op_return_render_connect_timeout_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="number" size="50" name="%s" value="%s" />',
      esc_attr( 'op_return_settings[connect_timeout_field]' ),
      esc_attr( $options['connect_timeout_field'] )
    );
}

function op_return_render_receive_timeout_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="number" size="50" name="%s" value="%s" />',
      esc_attr( 'op_return_settings[receive_timeout_field]' ),
      esc_attr( $options['receive_timeout_field'] )
    );
}

/*
//Add Custom Admin Menu Item and Sub Menus
function theme_options_panel(){
  add_menu_page('Theme page title', 'OP_RETURN', 'manage_options', 'theme-options', 'wps_theme_func');
  add_submenu_page( 'theme-options', 'Settings page title', 'Settings menu label', 'manage_options', 'theme-op-settings', 'wps_theme_func_settings');
  add_submenu_page( 'theme-options', 'FAQ page title', 'FAQ menu label', 'manage_options', 'theme-op-faq', 'wps_theme_func_faq');
}
add_action('admin_menu', 'theme_options_panel');

function wps_theme_func(){
                echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
                <h2>OP_RETURN Configuration</h2></div>';
}

function wps_theme_func_settings(){
                echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
                <h2>Settings</h2></div>';
}

function wps_theme_func_faq(){
                echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
                <h2>FAQ</h2></div>';
}
*/
?>