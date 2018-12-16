# Xeonbit Gateway for WooCommerce

## Features

* Payment validation done through either `xeonbit-wallet-rpc` or the [explorer.xeonbit.com blockchain explorer](https://explorer.xeonbit.com).
* Validates payments with `cron`, so does not require users to stay on the order confirmation page for their order to validate.
* Order status updates are done through AJAX instead of Javascript page reloads.
* Customers can pay with multiple transactions and are notified as soon as transactions hit the mempool.
* Configurable block confirmations, from `0` for zero confirm to `60` for high ticket purchases.
* Live price updates every minute; total amount due is locked in after the order is placed for a configurable amount of time (default 60 minutes) so the price does not change after order has been made.
* Hooks into emails, order confirmation page, customer order history page, and admin order details page.
* View all payments received to your wallet with links to the blockchain explorer and associated orders.
* Optionally display all prices on your store in terms of Xeonbit.
* Shortcodes! Display exchange rates in numerous currencies.

## Requirements

* Xeonbit wallet to receive payments - [GUI](https://github.com/xeonbit-project/xeonbit-gui/releases) - [CLI](https://github.com/xeonbit-project/xeonbit/releases) - [Paper]
* [BCMath](http://php.net/manual/en/book.bc.php) - A PHP extension used for arbitrary precision maths

## Installing the plugin

* Download the plugin from the [releases page](https://github.com/xeonbit-project/xeonbitwp) or clone with `git clone https://github.com/xeonbit-project/xeonbitwp`
* Unzip or place the `xeonbit-woocommerce-gateway` folder in the `wp-content/plugins` directory.
* Activate "Xeonbit Woocommerce Gateway" in your WordPress admin dashboard.
* It is highly recommended that you use native cronjobs instead of WordPress's "Poor Man's Cron" by adding `define('DISABLE_WP_CRON', true);` into your `wp-config.php` file and adding `* * * * * wget -q -O - https://yourstore.com/wp-cron.php?doing_wp_cron >/dev/null 2>&1` to your crontab.

## Option 1: Use your wallet address and viewkey

This is the easiest way to start accepting Xeonbit on your website. You'll need:

* Your Xeonbit wallet address starting with `4`
* Your wallet's secret viewkey

Then simply select the `viewkey` option in the settings page and paste your address and viewkey. You're all set!

Note on privacy: when you validate transactions with your private viewkey, your viewkey is sent to (but not stored on) xmrchain.net over HTTPS. This could potentially allow an attacker to see your incoming, but not outgoing, transactions if they were to get his hands on your viewkey. Even if this were to happen, your funds would still be safe and it would be impossible for somebody to steal your money. For maximum privacy use your own `xeonbit-wallet-rpc` instance.

## Option 2: Using `xeonbit-wallet-rpc`

The most secure way to accept Xeonbit on your website. You'll need:

* Root access to your webserver
* Latest [Xeonbit-currency binaries](https://github.com/xeonbit-project/xeonbit/releases)

After downloading (or compiling) the Xeonbit binaries on your server, install the [systemd unit files](https://github.com/xeonbit-project/xeonbitwp/tree/master/assets/systemd-unit-files) or run `xeonbitd` and `xeonbit-wallet-rpc` with `screen` or `tmux`. You can skip running `xeonbitd` by using a remote node with `xeonbit-wallet-rpc` by adding `--daemon-address node.xeonbitworld.com:18889` to the `xeonbit-wallet-rpc.service` file.

Note on security: using this option, while the most secure, requires you to run the Xeonbit wallet RPC program on your server. Best practice for this is to use a view-only wallet since otherwise your server would be running a hot-wallet and a security breach could allow hackers to empty your funds.

## Configuration

* `Enable / Disable` - Turn on or off Xeonbit gateway. (Default: Disable)
* `Title` - Name of the payment gateway as displayed to the customer. (Default: Xeonbit Gateway)
* `Discount for using Xeonbit` - Percentage discount applied to orders for paying with Xeonbit. Can also be negative to apply a surcharge. (Default: 0)
* `Order valid time` - Number of seconds after order is placed that the transaction must be seen in the mempool. (Default: 3600 [1 hour])
* `Number of confirmations` - Number of confirmations the transaction must recieve before the order is marked as complete. Use `0` for nearly instant confirmation. (Default: 5)
* `Confirmation Type` - Confirm transactions with either your viewkey, or by using `xeonbit-wallet-rpc`. (Default: viewkey)
* `Xeonbit Address` (if confirmation type is viewkey) - Your public Xeonbit address starting with 4. (No default)
* `Secret Viewkey` (if confirmation type is viewkey) - Your *private* viewkey (No default)
* `Xeonbit wallet RPC Host/IP` (if confirmation type is `xeonbit-wallet-rpc`) - IP address where the wallet rpc is running. It is highly discouraged to run the wallet anywhere other than the local server! (Default: 127.0.0.1)
* `Xeonbit wallet RPC port` (if confirmation type is `xeonbit-wallet-rpc`) - Port the wallet rpc is bound to with the `--rpc-bind-port` argument. (Default 18881)
* `Testnet` - Check this to change the blockchain explorer links to the testnet explorer. (Default: unchecked)
* `SSL warnings` - Check this to silence SSL warnings. (Default: unchecked)
* `Show QR Code` - Show payment QR codes. (Default: unchecked)
* `Show Prices in Xeonbit` - Convert all prices on the frontend to Xeonbit. Experimental feature, only use if you do not accept any other payment option. (Default: unchecked)
* `Display Decimals` (if show prices in Xeonbit is enabled) - Number of decimals to round prices to on the frontend. The final order amount will not be rounded and will be displayed down to the nanoXeonbit. (Default: 12)

## Shortcodes

This plugin makes available two shortcodes that you can use in your theme.

#### Live price shortcode

This will display the price of Xeonbit in the selected currency. If no currency is provided, the store's default currency will be used.

```
[xeonbit-price]
[xeonbit-price currency="BTC"]
[xeonbit-price currency="USD"]
[xeonbit-price currency="CAD"]
[xeonbit-price currency="EUR"]
[xeonbit-price currency="GBP"]
```
Will display:
```
1 XNB = 123.68000 USD
1 XNB = 0.01827000 BTC
1 XNB = 123.68000 USD
1 XNB = 168.43000 CAD
1 XNB = 105.54000 EUR
1 XNB = 94.84000 GBP
```


#### Xeonbit accepted here badge

This will display a badge showing that you accept Xeonbit-currency.

`[xeonbit-accepted-here]`

![Xeonbit Accepted Here](/assets/images/xeonbit-accepted-here.png?raw=true "Xeonbit Accepted Here")

## Donations

xeonbit-integrations: WcBv3auD1aPE1P13bnV4PwP5bYfn4WUC5N47SgfRbVC4VZh1r347j5hF3RnVusFhQxBmR6jasBhW6AuCFA8QKNkE1LAfBrKaW

