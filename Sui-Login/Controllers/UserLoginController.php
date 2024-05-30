<?php

namespace SuiLogin\Controllers;

class UserLoginController

{
  /**
   * Self Constructor object.
   * @var UserLoginController $instance
   */
  private static UserLoginController $instance;

  private function __construct()
  {
    add_action('wp_ajax_nopriv_loginWithSui', [$this, 'ajaxLoginWithSui']);
    add_action('wp_ajax_loginWithSui', [$this, 'ajaxLoginWithSui']);

    add_filter('wp_pre_insert_user_data', [$this, 'override_user_name'], 10, 4);
  }

  function override_user_name($data, $update, $id, $userdata)
  {
    // Check if it's a new user (not an update)
    if (!$update && isset($userdata['meta_input']['wallet_account'])) {
      $data['user_login'] = $userdata['meta_input']['wallet_account'];
    }

    return $data;
  }

  public function ajaxLoginWithSui()
  {
    // Initial response with default error message
    $response = [
      'verified' => false,
      'error' => 'Please provide a valid signature.',
    ];

    // Check if the signature is provided
    if (isset($_POST['signature'])) {
      $signature = stripslashes($_POST['signature']);
      $nonce = $this->getNonceMessage();

      // Verify the signature
      $res = sui_verify_personal_message($nonce, $signature);

      // Check if the verification was successful
      if ($res['verified'] === true) {
        $addr = sanitize_user($res['address']);
        $isValidAdress = sui_is_valid_address($addr);

        if (!$isValidAdress) {
          $response['error'] = 'User creation failed: invalid SUI address';
          wp_send_json($response);
          die;
        }


        $wallet = '0x' . $addr;
        $truncated = '0x' . $this->truncate($addr, 40, '-'); // Truncate to 40 characters
        $pretty = '0x' . $this->truncate($addr, 10, '...'); // Truncate to 40 characters

        $user = get_user_by('login', $wallet);

        // Create a new user if not exists
        if (!$user) {
          $user_id = wp_insert_user([
            'user_login' => $truncated,
            'user_nicename' => $truncated,
            'display_name' => $pretty,
            'user_pass' => wp_generate_password(12),
            'meta_input' => [
              'wallet_account' => $wallet,
            ]
          ]);

          // Check if user creation was successful
          if (is_wp_error($user_id)) {
            $response['error'] = 'User creation failed: ' . $user_id->get_error_message();
            wp_send_json($response);
            die;
          }

          $user = get_user_by('ID', $user_id);
        }

        // Clear existing authentication cookies and set new ones
        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);

        // Update response for successful verification
        $response = [
          'verified' => true,
          'error' => '',
        ];
      } else {
        // Verification failed
        $response['error'] = 'Signature verification failed.';
      }
    } else {
      // Signature not provided
      $response['error'] = 'Signature not provided.';
    }

    // Send JSON response
    wp_send_json($response);
    die;
  }

  public function truncate($string, $length, $sep = ''): string
  {
    $string_length = strlen($string);

    if ($string_length <= $length) {
      return $string;
    }

    $left_length = floor(($length - 2) / 2);
    $right_length = ceil(($length - 2) / 2);

    $left_part = substr($string, 0, $left_length);
    $right_part = substr($string, -$right_length);

    return $left_part . $sep . $right_part;
  }


  public function getNonceMessage(): string
  {
    $nonce_message = "Sign this message to verify your account - ";
    $nonce = $nonce_message . wp_create_nonce('sui_login_nonce');

    return "$nonce";
  }

  /**
   * Get self object
   *
   * @return UserLoginController object
   */
  public static function getInstance(): UserLoginController
  {
    if (empty(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }
}
