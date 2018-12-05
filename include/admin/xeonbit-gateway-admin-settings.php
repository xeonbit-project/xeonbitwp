<?php

defined( 'ABSPATH' ) || exit;

return array(
    'enabled' => array(
        'title' => __('Enable / Disable', 'xeonbit_gateway'),
        'label' => __('Enable this payment gateway', 'xeonbit_gateway'),
        'type' => 'checkbox',
        'default' => 'no'
    ),
    'title' => array(
        'title' => __('Title', 'xeonbit_gateway'),
        'type' => 'text',
        'desc_tip' => __('Payment title the customer will see during the checkout process.', 'xeonbit_gateway'),
        'default' => __('Xeonbit Gateway', 'xeonbit_gateway')
    ),
    'description' => array(
        'title' => __('Description', 'xeonbit_gateway'),
        'type' => 'textarea',
        'desc_tip' => __('Payment description the customer will see during the checkout process.', 'xeonbit_gateway'),
        'default' => __('Pay securely using Xeonbit. You will be provided payment details after checkout.', 'xeonbit_gateway')
    ),
    'discount' => array(
        'title' => __('Discount for using Xeonbit', 'xeonbit_gateway'),
        'desc_tip' => __('Provide a discount to your customers for making a private payment with Xeonbit', 'xeonbit_gateway'),
        'description' => __('Enter a percentage discount (i.e. 5 for 5%) or leave this empty if you do not wish to provide a discount', 'xeonbit_gateway'),
        'type' => __('number'),
        'default' => '0'
    ),
    'valid_time' => array(
        'title' => __('Order valid time', 'xeonbit_gateway'),
        'desc_tip' => __('Amount of time order is valid before expiring', 'xeonbit_gateway'),
        'description' => __('Enter the number of seconds that the funds must be received in after order is placed. 3600 seconds = 1 hour', 'xeonbit_gateway'),
        'type' => __('number'),
        'default' => '3600'
    ),
    'confirms' => array(
        'title' => __('Number of confirmations', 'xeonbit_gateway'),
        'desc_tip' => __('Number of confirms a transaction must have to be valid', 'xeonbit_gateway'),
        'description' => __('Enter the number of confirms that transactions must have. Enter 0 to zero-confim. Each confirm will take approximately four minutes', 'xeonbit_gateway'),
        'type' => __('number'),
        'default' => '5'
    ),
    'confirm_type' => array(
        'title' => __('Confirmation Type', 'xeonbit_gateway'),
        'desc_tip' => __('Select the method for confirming transactions', 'xeonbit_gateway'),
        'description' => __('Select the method for confirming transactions', 'xeonbit_gateway'),
        'type' => 'select',
        'options' => array(
            'viewkey'        => __('viewkey', 'xeonbit_gateway'),
            'xeonbit-wallet-rpc' => __('xeonbit-wallet-rpc', 'xeonbit_gateway')
        ),
        'default' => 'viewkey'
    ),
    'xeonbit_address' => array(
        'title' => __('Xeonbit Address', 'xeonbit_gateway'),
        'label' => __('Useful for people that have not a daemon online'),
        'type' => 'text',
        'desc_tip' => __('Xeonbit Wallet Address (XeonbitL)', 'xeonbit_gateway')
    ),
    'viewkey' => array(
        'title' => __('Secret Viewkey', 'xeonbit_gateway'),
        'label' => __('Secret Viewkey'),
        'type' => 'text',
        'desc_tip' => __('Your secret Viewkey', 'xeonbit_gateway')
    ),
    'daemon_host' => array(
        'title' => __('Xeonbit wallet RPC Host/IP', 'xeonbit_gateway'),
        'type' => 'text',
        'desc_tip' => __('This is the Daemon Host/IP to authorize the payment with', 'xeonbit_gateway'),
        'default' => '127.0.0.1',
    ),
    'daemon_port' => array(
        'title' => __('Xeonbit wallet RPC port', 'xeonbit_gateway'),
        'type' => __('number'),
        'desc_tip' => __('This is the Wallet RPC port to authorize the payment with', 'xeonbit_gateway'),
        'default' => '18881',
    ),
    'testnet' => array(
        'title' => __(' Testnet', 'xeonbit_gateway'),
        'label' => __(' Check this if you are using testnet ', 'xeonbit_gateway'),
        'type' => 'checkbox',
        'description' => __('Advanced usage only', 'xeonbit_gateway'),
        'default' => 'no'
    ),
    'onion_service' => array(
        'title' => __(' SSL warnings ', 'xeonbit_gateway'),
        'label' => __(' Check to Silence SSL warnings', 'xeonbit_gateway'),
        'type' => 'checkbox',
        'description' => __('Check this box if you are running on an Onion Service (Suppress SSL errors)', 'xeonbit_gateway'),
        'default' => 'no'
    ),
    'show_qr' => array(
        'title' => __('Show QR Code', 'xeonbit_gateway'),
        'label' => __('Show QR Code', 'xeonbit_gateway'),
        'type' => 'checkbox',
        'description' => __('Enable this to show a QR code after checkout with payment details.'),
        'default' => 'no'
    ),
    'use_xeonbit_price' => array(
        'title' => __('Show Prices in Xeonbit', 'xeonbit_gateway'),
        'label' => __('Show Prices in Xeonbit', 'xeonbit_gateway'),
        'type' => 'checkbox',
        'description' => __('Enable this to convert ALL prices on the frontend to Xeonbit (experimental)'),
        'default' => 'no'
    ),
    'use_xeonbit_price_decimals' => array(
        'title' => __('Display Decimals', 'xeonbit_gateway'),
        'type' => __('number'),
        'description' => __('Number of decimal places to display on frontend. Upon checkout exact price will be displayed.'),
        'default' => 12,
    ),
);
