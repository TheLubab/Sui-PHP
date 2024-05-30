  <div class="sui-wrap">
    <h1 class="sui-h1">
      Payments Settings
    </h1>
    <form method="post" action="" class="sui-settings-form">
      <div class="sui-form-field">
        <label class="sui-label" for="login_button_text">
          Default Payment Address
        </label>
        <input class="sui-input" required="required" type="text" name="receiver" placeholder="0x..." value="<?php echo esc_attr($pay_receiver); ?>" />
      </div>
      <?php submit_button(); ?>
    </form>
  </div>
