=== Xeonbit WooCommerce Extension ===
Contributors: Xeonbit Monero Integrations Team, Ryo Currency Project
Tags: xeonbit, woocommerce, integration, payment, merchant, cryptocurrency, accept xeonbit, xeonbit woocommerce
Requires at least: 4.0
Tested up to: 4.9.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Xeonbit WooCommerce Extension is a Wordpress plugin that allows to accept bitcoins at WooCommerce-powered online stores.

== Description ==

An extension to WooCommerce for accepting Xeonbit as payment in your store.

= Benefits =

* Accept payment directly into your personal Xeonbit wallet.
* Accept payment in xeonbit for physical and digital downloadable products.
* Add xeonbit payments option to your existing online store with alternative main currency.
* Flexible exchange rate calculations fully managed via administrative settings.
* Zero fees and no commissions for xeonbit payments processing from any third party.
* Automatic conversion to Xeonbit via real time exchange rate feed and calculations.
* Ability to set exchange rate calculation multiplier to compensate for any possible losses due to bank conversions and funds transfer fees.

== Installation ==

1. Install "Xeonbit WooCommerce extension" WordPress plugin just like any other WordPress plugin.
2. Activate
3. Setup your xeonbit-wallet-rpc with a view-only wallet
4. Add your xeonbit-wallet-rpc host address and Xeonbit address in the settings panel
5. Click “Enable this payment gateway”
6. Enjoy it!

== Remove plugin ==

1. Deactivate plugin through the 'Plugins' menu in WordPress
2. Delete plugin through the 'Plugins' menu in WordPress

== Screenshots == 
1. Xeonbit Payment Box
2. Xeonbit Options

== Changelog ==

= 0.1 =
* First version ! Yay!

= 0.2 =
* Bug fixes

= 0.3 =
* Complete rewrite of how the plugin handles payments

== Upgrade Notice ==

soon

== Frequently Asked Questions ==

* What is Xeonbit ?
Xeonbit is completely private, cryptographically secure, digital cash used across the globe. See https://getxeonbit.org for more information

* What is a Xeonbit wallet?
A Xeonbit wallet is a piece of software that allows you to store your funds and interact with the Xeonbit network. You can get a Xeonbit wallet from https://getxeonbit.org/downloads

* What is xeonbit-wallet-rpc ?
The xeonbit-wallet-rpc is an RPC server that will allow this plugin to communicate with the Xeonbit network. You can download it from https://github.com/xeonbit-project/xeonbit/ with the command-line tools.

* Why do I see `[ERROR] Failed to connect to xeonbit-wallet-rpc at localhost port 18881
Syntax error: Invalid response data structure: Request id: 1 is different from Response id: ` ?
This is most likely because this plugin can not reach your xeonbit-wallet-rpc. Make sure that you have supplied the correct host IP and port to the plugin in their fields. If your xeonbit-wallet-rpc is on a different server than your wordpress site, make sure that the appropriate port is open with port forwarding enabled.
