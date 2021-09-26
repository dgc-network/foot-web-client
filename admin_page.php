<?php

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

//    
function nelio_add_settings_page() {
    add_options_page(
      'OP_RETURN Settings',
      'OP_RETURN',
      'manage_options',
      'op-return-page',
      'nelio_render_settings_page'
    );
}
add_action( 'admin_menu', 'nelio_add_settings_page' );

//
function nelio_render_settings_page() {
?>
    <h2>OP_RETURN Settings</h2>
    <form action="options.php" method="post">
        <?php 
        settings_fields( 'op_return_settings' );
        //do_settings_sections( 'op_return_page' );
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

function nelio_register_settings() {
    register_setting(
        'op_return_settings',
        'op_return_settings',
        'nelio_validate_example_plugin_settings'
    );
/*  
    add_settings_section(
        'section_one',
        'Section One',
        'nelio_section_one_text',
        'op_return_page'
    );
*/  
    add_settings_field(
        'some_text_field',
        'Some Text Field',
        'nelio_render_some_text_field',
        'op_return_page',
        //'section_one'
    );
  
    add_settings_field(
        'another_number_field',
        'Another Number Field',
        'nelio_render_another_number_field',
        'op_return_page',
        //'section_one'
    );
}
add_action( 'admin_init', 'nelio_register_settings' );

function nelio_validate_example_plugin_settings( $input ) {
    $output['some_text_field']      = sanitize_text_field( $input['some_text_field'] );
    $output['another_number_field'] = absint( $input['another_number_field'] );
    // ...
    return $output;
}

function nelio_section_one_text() {
    echo '<p>This is the first (and only) section in my settings.</p>';
}
  
function nelio_render_some_text_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="text" name="%s" value="%s" />',
      esc_attr( 'op_return_settings[some_text_field]' ),
      esc_attr( $options['some_text_field'] )
    );
}
  
function nelio_render_another_number_field() {
    $options = get_option( 'op_return_settings' );
    printf(
      '<input type="number" name="%s" value="%s" />',
      esc_attr( 'op_return_settings[another_number_field]' ),
      esc_attr( $options['another_number_field'] )
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