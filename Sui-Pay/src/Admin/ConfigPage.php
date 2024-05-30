<?php

namespace SuiPay\Admin;

use function SuiPay\Helper\render_view;

/**
 * Class ConfigPage
 * @package SuiPay\Admin
 */
class ConfigPage
{

  public function __construct()
  {
    add_action('admin_menu', [&$this, 'addAdminPage']);
    add_action('admin_init', [&$this, 'registerSettings']);
  }

  public function addAdminPage()
  {
    add_submenu_page(
      'sui-config',
      'Sui payments config',
      'Payments',
      'manage_options',
      'sui-pay-config',
      [$this, 'displaySettingsPage']
    );
  }

  public function registerSettings()
  {
    register_setting('sui-payment-settings', 'receiver');
  }


  public function displaySettingsPage()
  {
    if (!current_user_can('manage_options')) {
      return;
    }

    if (isset($_POST['receiver'])) {
      update_option('receiver', sanitize_text_field($_POST['receiver']));
    }

    $pay_receiver = get_option('pay_receiver', '');

    $path = plugin_dir_path(__FILE__) . './views/pay-with-sui-config.php';
    render_view($path, [
      "pay_receiver" => $pay_receiver
    ]);
  }
}
