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
                $customWPMenu = new WordPressMenu( array(
                    'slug' => 'wpmenu',
                    'title' => 'WP Menu',
                    'desc' => 'Settings for theme custom WordPress Menu',
                    'icon' => 'dashicons-welcome-widgets-menus',
                    'position' => 99,
                ));
        
        $customWPMenu->add_field(array(
            'name' => 'text',
            'title' => 'Text Input',
            'desc' => 'Input Description' ));
        
        $customWPMenu->add_field(array(
            'name' => 'checkbox',
            'title' => 'Checkbox Example',
            'desc' => 'Check it to wake it',
            'type' => 'checkbox'));
}

function wps_theme_func_settings(){
                echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
                <h2>Settings</h2></div>';
}

function wps_theme_func_faq(){
                echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
                <h2>FAQ</h2></div>';
}
?>