<?php

namespace SuiPay\ShortCode;

use function SuiPay\Helper\render_view;

class ShortCode
{

  public function __construct()
  {
    add_shortcode('sui_pay', [$this, 'suiPay']);
    add_shortcode('sui_donation_box', [$this, 'donationBox']);
  }

  public function suiPay($atts = []): string
  {
    $pay_receiver = get_option('pay_receiver', '');

    $atts = shortcode_atts(
      [
        'label' => 'Pay With Sui',
        'amount' => '1',
        'currency' => 'SUI',
        'receiver' => $pay_receiver,
      ],
      $atts,
      'sui_pay'
    );

    // Sanitize the amount
    $amount = floatval($atts['amount']);
    $amount = $amount > 0 ? $amount : 1; // Ensure the amount is positive

    $receiver = sanitize_text_field($atts['receiver']);

    // Sanitize the text
    $label = sanitize_text_field($atts['label']);

    $path = plugin_dir_path(__FILE__) . 'views/pay-button.php';

    ob_start();
    render_view($path, [
      'label' => $label,
      'amount' => $amount,
      'receiver' => $receiver,
    ]);
    return ob_get_clean();
  }

  public function donationBox($atts = []): string
  {
    $pay_receiver = get_option('pay_receiver', '');

    $atts = shortcode_atts(
      [
        'title' => 'Donate With Sui',
        'button' => 'Donate Now',
        'receiver' => $pay_receiver,
      ],
      $atts,
      'sui_donation_box'
    );

    // Sanitize the text
    $title = sanitize_text_field($atts['title']);
    $button = sanitize_text_field($atts['button']);
    $receiver = sanitize_text_field($atts['receiver']);

    $path = plugin_dir_path(__FILE__) . 'views/donation-box.php';

    ob_start();
    render_view($path, [
      'button' => $button,
      'title' => $title,
      'receiver' => $receiver,
    ]);
    return ob_get_clean();
  }
}
