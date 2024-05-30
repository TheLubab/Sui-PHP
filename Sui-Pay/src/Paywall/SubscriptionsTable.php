<?php

namespace SuiPay\Paywall;

use WP_List_Table;

class SubscriptionsTable extends WP_List_Table
{
  // Define constructor
  function __construct()
  {
    parent::__construct(array(
      'singular' => 'subscription',
      'plural' => 'subscriptions',
      'ajax' => false
    ));
  }

  // Prepare the items
  function prepare_items()
  {
    global $wpdb;

    $table_name = $wpdb->prefix . 'sui_pay_subscriptions';
    $per_page = 10;

    $columns = $this->get_columns();
    // $hidden = array();
    // $sortable = $this->get_sortable_columns();

    $this->_column_headers = array($columns);

    $current_page = $this->get_pagenum();

    $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

    $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY id DESC LIMIT %d OFFSET %d", $per_page, ($current_page - 1) * $per_page));

    $this->items = $data;

    $this->set_pagination_args(array(
      'total_items' => $total_items,
      'per_page' => $per_page,
      'total_pages' => ceil($total_items / $per_page)
    ));
  }

  function prepare_items_for_user($username)
  {
    if (!$username) {
      return;
    }
    global $wpdb;

    $table_name = $wpdb->prefix . 'sui_pay_subscriptions';
    $per_page = 10;

    $columns = $this->get_columns();
    // $hidden = array();
    // $sortable = $this->get_sortable_columns();

    $this->_column_headers = array($columns);

    $current_page = $this->get_pagenum();

    $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

    $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_address = %s ORDER BY id DESC LIMIT %d OFFSET %d", $username, $per_page, ($current_page - 1) * $per_page));

    $this->items = $data;

    $this->set_pagination_args(array(
      'total_items' => $total_items,
      'per_page' => $per_page,
      'total_pages' => ceil($total_items / $per_page)
    ));
  }

  // Define columns
  function get_columns()
  {
    return array(
      'user_address' => 'User Address',
      'receiver' => 'Receiver',
      'subscription_start' => 'Subscription Start',
      'subscription_end' => 'Subscription End',
      'digest' => 'Digest',
      'amount' => 'Amount'
    );
  }

  // Define column default
  function column_default($item, $column_name)
  {
    return isset($item->$column_name) ? $item->$column_name : '';
  }
}
