<?php

/**
 * Plugin Name: Pay With Sui
 * Description: web3 payments with SUI
 * Version: 0.0.1
 * Author: Sekaiking
 */

use SuiPay\Main;

$composerLoader =  __DIR__ . '/vendor/autoload.php';
if (file_exists($composerLoader)) {
  require_once $composerLoader;
} else {
  _e('Install composer for current work');
  exit;
}

register_activation_hook(__FILE__, 'child_plugin_activate');

function child_plugin_activate()
{
  if (!is_plugin_active('SUI-Login/index.php') and current_user_can('activate_plugins')) {
    wp_die('This plugin requires the "Login With Sui" Plugin to be installed and active. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
  }
}


function sui_pay_create_subscription_table()
{
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $db_table_name = $wpdb->prefix . 'sui_pay_subscriptions';

  if ($wpdb->get_var("show tables like '$db_table_name'") != $db_table_name) {
    $sql = "CREATE TABLE $db_table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      user_address varchar(255) NOT NULL,
      receiver varchar(255) NOT NULL,
      subscription_start datetime NOT NULL,
      subscription_end datetime NOT NULL,
      digest varchar(255) NOT NULL,
      amount varchar(255) NOT NULL,
      PRIMARY KEY  (id)
        ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}
register_activation_hook(__FILE__, 'sui_pay_create_subscription_table');

Main::getInstance(__DIR__);
