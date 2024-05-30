<?php

namespace SuiPay\Paywall;

use DateTime;

use function SuiPay\Helper\render_view;
use function SuiPay\Helper\execute_transaction_block;
use function SuiPay\Helper\fetch_sui_transaction_block;

/**
 * Class Paywall
 * @package SuiPay\Paywall
 */
class Paywall
{
  public function __construct()
  {
    add_action('admin_menu', [$this, 'add_settings_page']);
    add_action('admin_init', [$this, 'register_settings']);

    add_action('admin_menu', [$this, 'add_user_dashboard_page']);

    add_filter('the_content', [$this, 'check_paywall']);

    add_action('wp_ajax_sui_process_subscribe', [$this, 'process_subscribe']);
    add_action('wp_ajax_nopriv_sui_process_subscribe', [$this, 'process_subscribe']);
  }

  public function add_settings_page()
  {
    add_submenu_page(
      'sui-config',
      'Sui Paywall Config',
      'Paywall',
      'manage_options',
      'sui-paywall-config',
      [$this, 'renderConfigPage']
    );

    add_submenu_page(
      'sui-config',
      'Sui Paywall Subscribers',
      'Paywall Subscribers',
      'manage_options',
      'sui-paywall-subscriptions',
      [$this, 'renderSubsPage']
    );
  }

  public function add_user_dashboard_page()
  {
    $user = wp_get_current_user();
    $username = $user->user_login;
    if (str_starts_with($username, "0x")) {
      add_menu_page(
        'My Subscriptions',
        'My Subscriptions',
        'read',
        'sui-user-subscriptions',
        [$this, 'render_user_dashboard_subscriptions_page'],
        'dashicons-list-view',
        20
      );
    }
  }

  public function render_user_dashboard_subscriptions_page()
  {
    if (!is_user_logged_in()) {
      wp_die('You need to be logged in to view this page.');
    }

    $user = wp_get_current_user();

?>
    <div class="wrap">
      <h1 class="sui-h1">Subscriptions history</h1>
      <?php
      $subscriptions_table = new SubscriptionsTable();
      $subscriptions_table->prepare_items_for_user($user->user_login);
      $subscriptions_table->display();
      ?>
    </div>
  <?php
  }

  public function register_settings()
  {
    register_setting('sui-paywall-settings', 'paywall_type');
    register_setting('sui-paywall-settings', 'paywall_receiver');
    register_setting('sui-paywall-settings', 'paywall_price');
    register_setting('sui-paywall-settings', 'paywall_categories');
  }

  function renderSubsPage()
  {
  ?>
    <div class="wrap">
      <h1 class="sui-h1">Subscriptions List</h1>
      <?php
      $subscriptions_table = new SubscriptionsTable();
      $subscriptions_table->prepare_items();
      $subscriptions_table->display();
      ?>
    </div>
<?php
  }

  public function renderConfigPage()
  {
    if (!current_user_can('manage_options')) {
      return;
    }

    if (isset($_POST['paywall_price'])) {
      update_option('paywall_price', sanitize_text_field($_POST['paywall_price']));
    }
    if (isset($_POST['paywall_receiver'])) {
      update_option('paywall_receiver', sanitize_text_field($_POST['paywall_receiver']));
    }
    if (isset($_POST['paywall_type'])) {
      update_option('paywall_type', sanitize_text_field($_POST['paywall_type']));
    }
    if (isset($_POST['paywall_categories'])) {
      update_option('paywall_categories', $_POST['paywall_categories']);
    }

    $categories = get_categories(['hide_empty' => 0]);
    $paywall_categories = get_option('paywall_categories', []);
    $paywall_price = get_option('paywall_price', '1');
    $paywall_type = get_option('paywall_type', 'one-time');
    $paywall_receiver = get_option('paywall_receiver', '');


    $path = plugin_dir_path(__FILE__) . 'views/paywall-config-page.php';

    render_view($path, [
      'categories'  => $categories,
      'paywall_categories'  => $paywall_categories,
      'paywall_price' => $paywall_price,
      'paywall_type' => $paywall_type,
      'paywall_receiver' => $paywall_receiver,
    ]);
  }

  public function check_paywall($content)
  {
    if (is_single() || is_category()) {
      global $post;
      $categories = get_the_category($post->ID);
      $paywall_categories = get_option('paywall_categories', []);
      $paywall_type = get_option('paywall_type', '');
      $paywall_price = get_option('paywall_price', '');
      $paywall_receiver = get_option('paywall_receiver', '');

      foreach ($categories as $category) {
        if (in_array($category->term_id, $paywall_categories)) {
          if (!get_current_user_id()) {
            $path = plugin_dir_path(__FILE__) . 'views/paywall-guest.php';
            ob_start();
            render_view($path, []);
            return ob_get_clean();
          }
          if (!$this->user_has_access()) {
            $path = plugin_dir_path(__FILE__) . 'views/paywall-not-subscribed.php';
            ob_start();
            render_view($path, [
              'paywall_price' => $paywall_price,
              'paywall_type' => $paywall_type,
              'paywall_receiver' => $paywall_receiver,
            ]);
            return ob_get_clean();
          }
        }
      }
    }
    return $content;
  }

  public function user_has_access()
  {
    global $wpdb;
    $user = wp_get_current_user();
    if (!$user->exists()) {
      return false;
    }

    $username = $user->user_login;
    $table_name = $wpdb->prefix . 'sui_pay_subscriptions';
    $current_time = current_time('mysql');

    $query = $wpdb->prepare("
        SELECT COUNT(*) 
        FROM $table_name 
        WHERE user_address = %s 
          AND subscription_end > %s
    ", $username, $current_time);

    $subscription_count = $wpdb->get_var($query);

    return $subscription_count > 0;
  }

  public function process_subscribe()
  {
    // Initial response with default error message
    $response = [
      'success' => false,
      'error' => '',
    ];

    if (isset($_POST['digest'])) {
      $digest = sanitize_text_field($_POST['digest']);
      $retryAttempts = 5;
      $retryDelay = 4;
      $result = null;

      for ($i = 0; $i < $retryAttempts; $i++) {
        $result = fetch_sui_transaction_block($digest, "testnet");

        if (isset($result['error'])) {
          break;
        } elseif (
          !isset($result['data']['transactionBlock']['effects']['timestamp'])
        ) {
          // Wait before retrying
          sleep($retryDelay);
        } else {
          break;
        }
      }

      if (isset($result['error'])) {
        $response['error'] = $result['error'];
      } else {
        // Validate the transaction
        $betterTx = $this->validate_subscription($result['data']['transactionBlock']);

        if ($betterTx['valid']) {
          $sender = $betterTx['sender'];
          $amount = $betterTx['amount'];
          $receiver = $betterTx['receiver'];
          $timestamp = $betterTx['timestamp'];

          if ($this->add_subscription($sender, $digest, $timestamp, $amount, $receiver)) {
            $response['success'] = true;
            $response['result'] = $result;
          } else {
            $response['error'] = 'Failed to add subscription for the user.';
          }
        } else {
          $response['error'] = $betterTx['error'];
        }
      }
    } else {
      $response['error'] = 'Please provide txBytes and signatures.';
    }

    // Send JSON response
    wp_send_json($response);
    die();
  }

  public function validate_subscription($transaction)
  {
    $paywall_price = get_option('paywall_price', '1');
    $paywall_receiver = get_option('paywall_receiver', '');

    $result = [
      'valid' => false,
      'error' => null,
      'sender' => $transaction['sender']['address'],
      'receiver' => null,
      'amount' => null,
      'timestamp' => $transaction['effects']['timestamp']
    ];

    if (strtolower($transaction['effects']['status']) != 'success') {
      $result['error'] = 'transaction failed ' . $transaction['effects']['status'];
      return $result;
    }

    // Check if the transaction has a receiver address
    if (!isset($result['sender'])) {
      $result['error'] = 'unvalid sender';
      return $result;
    }

    // get receiver change
    foreach ($transaction['effects']['balanceChanges']['nodes'] as $balanceChange) {
      if (strtolower($balanceChange['owner']['address']) == strtolower($paywall_receiver)) {
        $receiver_change = $balanceChange;
        $result['receiver'] = $paywall_receiver;
      }
    }

    if (!isset($receiver_change)) {
      $result['error'] = 'receiver balance didn\'t change';
      return $result;
    }

    // Check if the transaction has a correct coin type 
    $coinType = '0x0000000000000000000000000000000000000000000000000000000000000002::sui::SUI';
    if (strtolower($receiver_change['coinType']['repr']) !== strtolower($coinType)) {
      $result['error'] = 'received wrong token';
      return $result;
    }

    $ramount = (int)$receiver_change['amount'] / 1e9;
    $result['amount'] = $ramount;
    if ($ramount < (int)$paywall_price) {
      $result['error'] = 'received less than expected. expected: ' . (int)$paywall_price . ' received: ' . $ramount;
      return $result;
    }

    // Check if the transaction has a valid timestamp
    if (!isset($result['timestamp'])) {
      $result['timestamp'] = current_time('timestamp');
    }

    // All validation checks passed
    $result['valid'] = true;
    return $result;
  }

  public function add_subscription($address, $digest, $start, $amount, $receiver)
  {
    global $wpdb;

    // Ensure start is a valid date string
    $start_date = new DateTime($start);

    // Table name
    $table_name = $wpdb->prefix . 'sui_pay_subscriptions';

    $paywall_type = get_option('paywall_type', 'one-time');

    // Calculate the end date based on the paywall type
    if ($paywall_type == 'subscription') {
      $end_date = clone $start_date;
      $end_date->modify('+1 month');
    } else {
      $end_date = clone $start_date;
      $end_date->modify('+10 years');
    }

    // Convert DateTime objects to MySQL formatted strings
    $start = $start_date->format('Y-m-d H:i:s');
    $end = $end_date->format('Y-m-d H:i:s');

    // Data to insert
    $data = array(
      'user_address' => $address,
      'subscription_start' => $start,
      'subscription_end' => $end,
      'digest' => $digest,
      'amount' => $amount,
      'receiver' => $receiver,
    );

    // Insert the data into the database
    $success = $wpdb->insert($table_name, $data);

    if ($success === false) {
      return false;
    } else {
      return true;
    }
  }
}
