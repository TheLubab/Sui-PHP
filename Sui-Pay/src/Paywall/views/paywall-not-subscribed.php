<div class="wp-block-post-content">
  <p style="margin-bottom: .4rem;">
    This content is for supporters only. Please subscribe to access it.
  </p>
  <?php if ($paywall_type == 'subscription') { ?>
    <button class='sui-button sui-button-primary sui-paywall-button' data-amount="<?php echo esc_attr($paywall_price); ?>" data-receiver="<?php echo esc_attr($paywall_receiver); ?>">
      Subscribe for <?php echo $paywall_price; ?> Sui/month
    </button>
  <?php } else { ?>
    <button class='sui-button sui-button-primary sui-paywall-button' data-amount="<?php echo esc_attr($paywall_price); ?>" data-receiver="<?php echo esc_attr($paywall_receiver); ?>">
      Get forever access for <?php echo $paywall_price; ?> Sui
    </button>
  <?php } ?>
</div>
<style>
  #comments {
    display: none;
  }

  .nocomments,
  .no-comments,
  .has-comments,
  .post-comments,
  .comments-link,
  .comments-area,
  .comment-respond,
  .comments-closed,
  .comments-wrapper,
  .wp-block-comments,
  .comments-area__wrapper,
  .wp-block-post-comments,
  .wp-block-comments-title,
  .wp-block-comment-template,
  .wp-block-comments-query-loop {
    display: none;
  }

  /** Blocksy **/
  li.meta-comments {
    display: none
  }
</style>
