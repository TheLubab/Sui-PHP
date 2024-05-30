<?php

/**
 * Plugin Name: Login With Sui
 * Description: web3 login with SUI
 * Version: 0.0.1
 * Author: Sekaiking
 */

use SuiLogin\Model\Constructor\Constructor;

$composerLoader =  __DIR__ . '/vendor/autoload.php';
if (file_exists($composerLoader)) {
  require_once $composerLoader;
} else {
  _e('Install composer for current work');
  exit;
}

Constructor::getInstance(__DIR__);

register_activation_hook(__FILE__, 'custom_update_user_login_column_length');
function custom_update_user_login_column_length()
{
  global $wpdb;
  $wpdb->query("ALTER TABLE $wpdb->users MODIFY user_login VARCHAR(70);");
}

add_action('wp_logout', 'custom_auto_redirect_after_logout');
function custom_auto_redirect_after_logout()
{
  wp_safe_redirect(home_url());
  exit;
}
